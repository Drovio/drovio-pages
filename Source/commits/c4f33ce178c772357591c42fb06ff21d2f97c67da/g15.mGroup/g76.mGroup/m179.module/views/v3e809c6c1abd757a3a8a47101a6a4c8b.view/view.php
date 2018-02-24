<?php
//#section#[header]
// Module Declaration
$moduleID = 179;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\profiler\activityLogger;
use \UI\Html\HTMLContent;

// Create content
$pageContent = new HTMLContent();
$pageContent->build("", "logsContainer");

$logEntries = activityLogger::getLogs(time());
//print_r($logEntries);
foreach ($logEntries as $time => $entry)
{
	// Create entry box
	$box = DOM::create("div", "", "", "logEntry");
	$pageContent->append($box);
	
	$timespan = DOM::create("span", date("d-m-Y H:i:s", $time), "", "logTime");
	DOM::append($box, $timespan);
	
	$logContent = DOM::create("pre", $entry, "", "logContent");
	DOM::append($box, $logContent);
}


return $pageContent->getReport();
//#section_end#
?>