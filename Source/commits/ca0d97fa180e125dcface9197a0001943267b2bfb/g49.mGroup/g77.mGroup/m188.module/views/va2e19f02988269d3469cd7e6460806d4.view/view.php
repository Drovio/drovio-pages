<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("INU", "Views");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\geoloc\datetimer;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\togglers\toggler;
use \INU\Views\fileExplorer;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (empty($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectRepositoryPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$page->build($projectTitle." | Repository Overview", "projectRepositoryPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}

// Get vcs object
$repository = $project->getRepository();
$vcs = new vcs($repository);
$vcsInfo = $vcs->getInfo();
$authors = $vcs->getAuthors();

// VCS Version
$vcsVersion = HTML::select(".vcsVersion")->item(0);
HTML::innerHTML($vcsVersion, "Repository Controller v".$vcsInfo['version']);

// Project Title
$projectNameTitle = HTML::select("h3.projectTitle")->item(0);
HTML::innerHTML($projectNameTitle, $projectTitle);


$navBar = HTML::select(".navBar")->item(0);

// Navigation attributes
$targetcontainer = "ovSections";
$targetgroup = "vcsNavGroup";
$navgroup = "vcsNav";

// Overview
$navTitle = DOM::create("div", "Overview", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "overview", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);
DOM::appendAttr($navTitle, "class", "selected");

// Commits
$navTitle = DOM::create("div", "Commits (".$vcsInfo['commits'].")", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "commits", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);

// Branches
$navTitle = DOM::create("div", "Branches (".$vcsInfo['branches'].")", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "branches", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);

// Releases
$navTitle = DOM::create("div", "Releases (".$vcsInfo['releases'].")", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "releases", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);

// Authors
$navTitle = DOM::create("div", "Authors (".count($authors).")", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "authors", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);


// Sections
$ovSections = HTML::select("#ovSections")->item(0);


// Overview
$navContainer = $page->getNavigationGroup("overview", $targetgroup);
DOM::append($ovSections, $navContainer);

// Build fileExplorer
$headBranch = $vcs->getHeadBranch();
$headPath = $repository."/branches/".$headBranch;
$fExplorer = new fileExplorer($headPath, "vcsOvExplorer_".$projectID, $projectName, $showHidden = TRUE);
$vcsOverview = $fExplorer->build("", FALSE)->get();
HTML::addClass($vcsOverview, "vcsOvExplorer");
DOM::append($navContainer, $vcsOverview);





// Create navigation container
$navContainer = $page->getNavigationGroup("branches", $targetgroup);
DOM::append($ovSections, $navContainer);

$title = DOM::create("p", "There is only one branch on your repository, 'master'. You can't create any branches on this repository yet.");
DOM::append($navContainer, $title);




// Create navigation container
$navContainer = $page->getNavigationGroup("commits", $targetgroup);
DOM::append($ovSections, $navContainer);

$branchName = "master";

$attr = array();
$attr['pid'] = $projectID;
$attr['bn'] = $branchName;
$commitsModuleContainer = $page->getModuleContainer($moduleID, $action = "repositoryCommits", $attr, $startup = TRUE, $containerID = "commitsContainer");
DOM::append($navContainer, $commitsModuleContainer);




// Create navigation container
$navContainer = $page->getNavigationGroup("releases", $targetgroup);
DOM::append($ovSections, $navContainer);

// Release button
$title = moduleLiteral::get($moduleID, "lbl_releaseBtn");
$releaseBtn = DOM::create("div", $title, "", "releaseBtn");
DOM::append($navContainer, $releaseBtn);
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($releaseBtn, $moduleID, "releaseProject", "", $attr);

$attr = array();
$attr['pid'] = $projectID;
$attr['bn'] = "master";
$releasesModuleContainer = $page->getModuleContainer($moduleID, $action = "repositoryReleases", $attr, $startup = TRUE, $containerID = "releaseContainer");
DOM::append($navContainer, $releasesModuleContainer);


// Create navigation container
$navContainer = $page->getNavigationGroup("authors", $targetgroup);
DOM::append($ovSections, $navContainer);

foreach ($authors as $authorInfo)
{
	if (is_array($authorInfo))
	{
		$url = url::resolve("developer", "/profile/index.php");
		$params = array();
		$params['id'] = $authorInfo['account'];
		$url = url::get($url, $params);
		$authorContent = $page->getWeblink($url, $authorInfo['name'], "_blank");
	}
	else
		$authorContent = $authorInfo;
	
	$authorRow = DOM::create("h3", $authorContent);
	DOM::append($navContainer, $authorRow);
}

return $page->getReport();
//#section_end#
?>