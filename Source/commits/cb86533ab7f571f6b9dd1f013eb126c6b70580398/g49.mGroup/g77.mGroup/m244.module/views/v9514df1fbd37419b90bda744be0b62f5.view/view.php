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
use \DEV\Projects\projectStatus;

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
	$page->build("Project Not Found", "statusHistoryPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($projectTitle." | ".$title, "statusHistoryPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get history log
$historyLog = projectStatus::getHistoryLog($projectID);

$currentStatus = 1;
if (!empty($historyLog))
	$currentStatus = $historyLog['current']['status'];


// Set current status
$currentContainer = HTML::select(".current")->item(0);
HTML::addClass($currentContainer, "st".$currentStatus);

// Set current status literal
$statusLiteral = moduleLiteral::get($moduleID, "lbl_status_st_".$currentStatus);
$currentTitle = HTML::select(".current .title")->item(0);
DOM::append($currentTitle, $statusLiteral);


// Get history log and insert entries
$listContainer = HTML::select(".statusHistory .list")->item(0);
foreach ($historyLog['history'] as $historyEntry)
{
	// Create entry
	$entry = DOM::create("div", "", "", "hentry st".$historyEntry['status']);
	DOM::append($listContainer, $entry);
	
	// Set icon
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($entry, $ico);
	
	// Description
	$statusLiteral = moduleLiteral::get($moduleID, "lbl_status_st_".$historyEntry['status']);
	$title = DOM::create("span", $statusLiteral, "", "title");
	DOM::append($entry, $title);
	
	// Date
	$live = datetimer::live($historyEntry['timestamp']);
	$timestamp = DOM::create("span", $live, "", "timestamp");
	DOM::append($entry, $timestamp);
}



// Return output
return $page->getReport();
//#section_end#
?>