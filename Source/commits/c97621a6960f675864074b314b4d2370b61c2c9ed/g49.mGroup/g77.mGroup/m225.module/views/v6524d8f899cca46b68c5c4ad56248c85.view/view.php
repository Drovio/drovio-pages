<?php
//#section#[header]
// Module Declaration
$moduleID = 225;

// Inner Module Codes
$innerModules = array();
$innerModules['publisher'] = 261;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Version\vcs;

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


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_overviewTitle", array(), FALSE);
$page->build($ovTitle." | ".$projectTitle, "projectOverviewPage", TRUE);


// Get project status
$prStatus = HTML::select(".projectOverview .projectStatus")->item(0);
$prStatusTitle = HTML::select(".projectOverview .projectStatus .title")->item(0);
switch ($projectInfo['online'])
{
	case 1:
		HTML::addClass($prStatus, "healthy");
		$title = moduleLiteral::get($moduleID, "lbl_projectOnline");
		break;
	case 0:
		HTML::addClass($prStatus, "error");
		$title = moduleLiteral::get($moduleID, "lbl_projectOffline");
		break;
}

// Set project status title
HTML::append($prStatusTitle, $title);


// Project Health
$prHealthTitle = HTML::select(".projectOverview .projectHealth .title")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_projectHealthy");
HTML::append($prHealthTitle, $title);


// Project publisher (for admins)
if (accountKey::validateGroup("PROJECT_ADMIN", $projectID, accountKey::PROJECT_KEY_TYPE))
{
	// Set action for publisher
	$projectPublisher = HTML::select(".quickActions .publish")->item(0);
	
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($projectPublisher, $innerModules['publisher'], "", "", $attr);
}
else
{
	// Remove quick actions
	$quickActions = HTML::select(".quickActions")->item(0);
	HTML::replace($quickActions, NULL);
}



// Fill stats

// Project ID
$projectIDHolder = HTML::select(".stats .sr.projectId .value")->item(0);
HTML::innerHTML($projectIDHolder, $projectID);

$releases = $project->getReleases();

// Project Last Release Version
$lastRelease = $releases[0];
if (empty($lastRelease))
	$projectVersion = " - ";
else
	$projectVersion = $lastRelease['version'];
$projectVersionHolder = HTML::select(".stats .sr.projectVersion .value")->item(0);
HTML::innerHTML($projectVersionHolder, $projectVersion);

// Project Last Release Date
if (empty($lastRelease))
	$date = DOM::create("span", " - ");
else
	$date = datetimer::live($lastRelease['time_created']);
$projectDateHolder = HTML::select(".stats .sr.projectReleaseDate .value")->item(0);
HTML::append($projectDateHolder, $date);

// Source Stats

// Head Branch
$vcs = new vcs($projectID);
$headBranch = $vcs->getHeadBranch();
$projectHeadBranch = HTML::select(".stats .sr.headBranch .value")->item(0);
HTML::innerHTML($projectHeadBranch, $headBranch);

// Head branch version
$branchReleases = $vcs->getReleases();
$headReleases = $branchReleases[$headBranch];
if (empty($headReleases['packages']))
	$branchVersion = " - ";
else
{
	$currentVersion = $headReleases['current'];
	$headVersion = $headReleases['packages']["v".$currentVersion]['version'];
	$headBuild = $headReleases['packages']["v".$currentVersion]['build'];
	$branchVersion = $headVersion."-".$headBuild;
}
$projectHeadVersion = HTML::select(".stats .sr.branchVersion .value")->item(0);
HTML::innerHTML($projectHeadVersion, $branchVersion);

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>