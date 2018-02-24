<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Literals");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Get whether the account is team admin
$projectAdmin = accountKey::validateGroup($groupName = "PROJECT_ADMIN", $context = $projectID, $type = accountKey::PROJECT_KEY_TYPE);

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "projectRepositoryPage", TRUE);

// Get vcs object
$vcs = new vcs($projectID);
$vcsInfo = $vcs->getInfo();
$authors = $vcs->getAuthors();
$branchName = $vcs->getHeadBranch();

// VCS Version
$vcsVersion = HTML::select(".vcsVersion")->item(0);
HTML::innerHTML($vcsVersion, "Repository Controller v2.2");


$navBar = HTML::select(".navBar")->item(0);

// Navigation attributes
$targetcontainer = "repoSectionContainer";
$targetgroup = "vcsNavGroup";
$navgroup = "vcsNav";

$sections = array();
$sections["overview"] = "repositoryOverview";
$sections["commits"] = "repositoryCommits";
$sections["branches"] = "repositoryBranching";
$sections["releases"] = "repositoryReleases";
$sections["stats"] = "repositoryStatistics";
$sectionsContainer = HTML::select("#".$targetcontainer)->item(0);
foreach ($sections as $class => $viewName)
{
	// Navigation item
	$ref = $class."_container";
	$navItem = HTML::select(".projectRepository .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer, $targetgroup, $navgroup, $display = "none");
	
	$attr = array();
	$attr['id'] = $projectID;
	$attr['pid'] = $projectID;
	$attr['bn'] = $branchName;
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $containerID = $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($sectionsContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

$releaseBtn = HTML::select(".projectRepository .wbutton.newRelease")->item(0);
if ($projectAdmin)
{
	// Release button
	$attr = array();
	$attr['id'] = $projectID;
	$attr['pid'] = $projectID;
	$actionFactory->setModuleAction($releaseBtn, $moduleID, "releaseProject", "", $attr, $loading = TRUE);
}
else
	HTML::replace($releaseBtn, NULL);
	
	
// Get counts
$commits = $vcs->getBranchCommits(vcs::MASTER_BRANCH);
$countItem = DOM::create("span", " (".count($commits).")");
$navItem = HTML::select(".projectRepository .menu .menu_item.commits")->item(0);
DOM::append($navItem, $countItem);

$releases = $vcs->getReleases();
$releasePackages = $releases[vcs::MASTER_BRANCH]['packages'];
$countItem = DOM::create("span", " (".count($releasePackages).")");
$navItem = HTML::select(".projectRepository .menu .menu_item.releases")->item(0);
DOM::append($navItem, $countItem);

$branches = $vcs->getBranches();
$countItem = DOM::create("span", " (".count($branches).")");
$navItem = HTML::select(".projectRepository .menu .menu_item.branches")->item(0);
DOM::append($navItem, $countItem);

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>