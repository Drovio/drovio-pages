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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "vcsBuildsContainer");

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Get vcs object
$vcs = new vcs($projectID);

// Get releases
$branchName = engine::getVar('branch');
$packageID = engine::getVar('pid');
$releases = $vcs->getReleases();
$packageReleases = $releases[$branchName]['packages'][$packageID]['releases'];
foreach ($packageReleases as $releaseID => $releaseInfo)
{
	$releaseRow = DOM::create("div", "", "release_".$branchName."_".$releaseID, "buildRow");
	$pageContent->append($releaseRow);
	
	$date = datetimer::live($releaseInfo['time']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($releaseRow, $releaseDate);
	
	$releaseTitle = DOM::create("h4", $releaseInfo['title']." (v".$releaseInfo['version']."-".$releaseInfo['build'].")", "", "releaseTitle");
	DOM::append($releaseRow, $releaseTitle);
}

// Show notification if releases are empty
if (count($packageReleases) == 0)
{
	$title = moduleLiteral::get($moduleID, "lbl_noVersionHistory");
	$header = DOM::create("h4", $title, "", "no_vh");
	DOM::append($versionHistory, $header);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>