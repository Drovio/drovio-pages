<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("INU", "Views");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Geoloc\datetimer;
use \API\Resources\url;
use \UI\Modules\MPage;
use \UI\Presentation\togglers\toggler;
use \INU\Views\fileExplorer;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
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
$vcs = new vcs($projectID);
$vcsInfo = $vcs->getInfo();
$authors = $vcs->getAuthors();

// VCS Version
$vcsVersion = HTML::select(".vcsVersion")->item(0);
HTML::innerHTML($vcsVersion, "Repository Controller v2.2");


$navBar = HTML::select(".navBar")->item(0);

// Navigation attributes
$targetcontainer = "ovSections";
$targetgroup = "vcsNavGroup";
$navgroup = "vcsNav";

// Overview
$navTitle = HTML::select(".navTitle.overview")->item(0);
NavigatorProtocol::staticNav($navTitle, "overview", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// Commits
$title = DOM::create("span", " (".$vcsInfo['commitsCount'].")");
$navTitle = HTML::select(".navTitle.commits")->item(0);
DOM::append($navTitle, $title);
NavigatorProtocol::staticNav($navTitle, "commits", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// Branches
$title = DOM::create("span", " (".$vcsInfo['branchesCount'].")");
$navTitle = HTML::select(".navTitle.branches")->item(0);
DOM::append($navTitle, $title);
NavigatorProtocol::staticNav($navTitle, "branches", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// Releases
$title = DOM::create("span", " (".$vcsInfo['releasesCount'].")");
$navTitle = HTML::select(".navTitle.releases")->item(0);
DOM::append($navTitle, $title);
NavigatorProtocol::staticNav($navTitle, "releases", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// Authors
$navTitle = HTML::select(".navTitle.stats")->item(0);
NavigatorProtocol::staticNav($navTitle, "statistics", $targetcontainer, $targetgroup, $navgroup, $display = "none");


// Sections
$ovSections = HTML::select("#ovSections")->item(0);


// Overview
$navContainer = $page->getNavigationGroup("overview", $targetgroup);
HTML::addClass($navContainer, "ovSectContainer");
DOM::append($ovSections, $navContainer);

// Build fileExplorer
$vcs->checkout(vcs::MASTER_BRANCH);
$headBranch = $vcs->getHeadBranch();

$repository = $project->getRepository();
$headPath = $repository."/branches/".$headBranch;
$fExplorer = new fileExplorer($headPath, "vcsOvExplorer_".$projectID, $projectName, $showHidden = TRUE);
$vcsOverview = $fExplorer->build("", FALSE)->get();
HTML::addClass($vcsOverview, "vcsOvExplorer");
DOM::append($navContainer, $vcsOverview);


// Get head branch
$branchName = $vcs->getHeadBranch();


// Add module containers (Commits, Branches, Releases, Statistics)
setNavigationContainer($page, $moduleID, $ovSections, "commits", $targetgroup, $projectID, $branchName, "repositoryCommits", "commitsContainer");
setNavigationContainer($page, $moduleID, $ovSections, "branches", $targetgroup, $projectID, $branchName, "repositoryBranching", "branchesContainer");
setNavigationContainer($page, $moduleID, $ovSections, "releases", $targetgroup, $projectID, $branchName, "repositoryReleases", "releaseContainer");
setNavigationContainer($page, $moduleID, $ovSections, "statistics", $targetgroup, $projectID, $branchName, "repositoryStatistics", "statsContainer");


return $page->getReport();






function setNavigationContainer($page, $moduleID, $ovSections, $navGroup, $targetGroup, $projectID, $branchName, $action, $containerID)
{
	// Create navigation container
	$navContainer = $page->getNavigationGroup($navGroup, $targetGroup);
	HTML::addClass($navContainer, "ovSectContainer");
	DOM::append($ovSections, $navContainer);
	
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['bn'] = $branchName;
	$moduleContainer = $page->getModuleContainer($moduleID, $action, $attr, $startup = TRUE, $containerID);
	DOM::append($navContainer, $moduleContainer);
}
//#section_end#
?>