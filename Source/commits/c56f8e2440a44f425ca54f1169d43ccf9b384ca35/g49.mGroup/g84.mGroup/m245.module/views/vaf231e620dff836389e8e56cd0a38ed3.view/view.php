<?php
//#section#[header]
// Module Declaration
$moduleID = 245;

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
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \DEV\Profiler\console;


// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "historyContainer", TRUE);


$hlist = HTML::select(".historyContent .history")->item(0);

// Load history log
$historyLog = console::getHistoryLog();
foreach ($historyLog as $log)
{
	$litem = DOM::create("div", "", $log['id'], "hentry");
	DOM::append($hlist, $litem);
	
	$lheader = DOM::create("div", "", "", "hhd");
	DOM::append($litem, $lheader);
	
	$title = DOM::create("div", "History Log", "", "title");
	DOM::append($lheader, $title);
	
	$preview = DOM::create("span", "preview", "", "preview");
	DOM::append($lheader, $preview);
	
	$loader = DOM::create("span", "LOAD", "", "loader");
	DOM::append($lheader, $loader);
	
	// Set action to loader
	$attr = array();
	$attr['hid'] = $log['id'];
	$actionFactory->setModuleAction($loader, $moduleID, "console", "#c_console", $attr);
	
	
	$timestamp = DOM::create("div", "", "", "timestamp");
	DOM::append($lheader, $timestamp);
	$live = datetimer::live($log['time']);
	DOM::append($timestamp, $live);
	
	$hData = console::getFromHistory($log['id']);
	$contents = DOM::create("pre", $hData['code'], "", "hContents noDisplay");
	DOM::append($litem, $contents);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>