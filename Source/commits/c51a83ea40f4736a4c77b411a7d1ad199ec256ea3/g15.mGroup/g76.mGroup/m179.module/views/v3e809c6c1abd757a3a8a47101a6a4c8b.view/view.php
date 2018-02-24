<?php
//#section#[header]
// Module Declaration
$moduleID = 179;

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
importer::import("UI", "Modules");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \DEV\Profiler\log\activityLogger;
use \UI\Modules\MContent;

// Create content
$pageContent = new MContent();
$pageContent->build("", "logsContainer");

$logEntries = array();
try
{
	$logEntries = activityLogger::getLogs(time());
}
catch (Exception $ex)
{
}
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