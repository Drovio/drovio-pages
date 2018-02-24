<?php
//#section#[header]
// Module Declaration
$moduleID = 244;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
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
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "releaseLogPage", TRUE);
	
// Get releases
$releases = $project->getReleases();
$releaseLog = $project->getReleaseLog();


// Show release log
$listContainer = HTML::select(".releaseLog .list")->item(0);
foreach ($releases as $release)
{
	$releaseContainer = DOM::create("div", "", "release_".$release['version'], "prel");
	DOM::append($listContainer, $releaseContainer);
	
	// Release Row
	$releaseRow = DOM::create("div", "", "", "releaseRow");
	DOM::append($releaseContainer, $releaseRow);
	
	$releaseTitle = DOM::create("h4", "Version v".$release['version'], "", "releaseTitle");
	DOM::append($releaseRow, $releaseTitle);
	
	$date = datetimer::live($release['time_created']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($releaseRow, $releaseDate);
	
	$changelog = DOM::create("p", $release['changelog'], "", "releaseChangelog");
	DOM::append($releaseRow, $changelog);
	
	// Release Log
	$logList = DOM::create("div", "", "release_".$release['version']."_log", "logList");
	DOM::append($releaseContainer, $logList);
	
	// Show release log
	$logs = $releaseLog[$release['version']];
	foreach ($logs as $log)
	{
		$releaseRow = DOM::create("div", "", "", "releaseRow");
		DOM::append($logList, $releaseRow);
		
		$releaseTitle = DOM::create("h4", "v".$release['version'], "", "releaseTitle");
		DOM::append($releaseRow, $releaseTitle);
		
		$date = datetimer::live($log['timestamp']);
		$releaseDate = DOM::create("div", $date, "", "releaseDate");
		DOM::append($releaseRow, $releaseDate);
		
		$changelog = DOM::create("p", $log['changelog'], "", "releaseChangelog");
		DOM::append($releaseRow, $changelog);
	}
}



// Return output
return $page->getReport();
//#section_end#
?>