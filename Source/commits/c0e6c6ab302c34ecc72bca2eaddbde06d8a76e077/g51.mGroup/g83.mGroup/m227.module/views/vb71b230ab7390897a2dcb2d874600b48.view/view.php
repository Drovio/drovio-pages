<?php
//#section#[header]
// Module Declaration
$moduleID = 227;

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
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \DEV\BugTracker\bugTracker;
use \API\Literals\moduleLiteral;
use \UI\Presentation\frames\windowFrame;


// Build Frame
$wFrame = new windowFrame();
// Header
$title = "Bug Overview";//moduleLiteral::get($moduleID, "lbl_createWebsite", FALSE);
$wFrame->build($title, $class = "windowFrame");

// Create Content
$container = DOM::create();
$wFrame->append($container); 

//
$bugger = new bugTracker($_GET['pid']);
$issue = $bugger->getBug($_GET['bid']);


$mainCol = DOM::create('div', '', '', 'window-main-col clearfix');
	$section = DOM::create('div', '', '', 'info');
		$title = DOM::create('div', $issue["title"], '', 'title');
		
		DOM::append($section, $title);
		$desc = DOM::create('div', $issue["description"], '', 'description');
		
		DOM::append($section, $desc);
	DOM::append($mainCol, $section);
	$section = DOM::create('div', '', '', 'solution');
		$text = $issue["notes"];
		if(empty($text))
		{
			$text = moduleLiteral::get($moduleID, "ntf_noSolutionYet");
			$class = 'noSolution';
		}
		$content = DOM::create('div', '', '', $class);
		DOM::append($content, $text);
		DOM::append($section, $content);
	DOM::append($mainCol, $section);
DOM::append($container, $mainCol);

$sideCol = DOM::create('div', '', '', 'window-sidebar');
	$section = DOM::create('div', '', '', 'window-module');
		$title = DOM::create('div', '', '', '');
		DOM::append($section, $title);
		$row = DOM::create('div', '', '', 'row');
			$key = DOM::create('div', '', '', 'rowKey');
				$literal = moduleLiteral::get($moduleID, "lbl_status");
				DOM::append($key, $literal);
			DOM::append($section, $key);
			$value = DOM::create('div', $issue["status"], '', 'rowValue status '.$issue["status"]);			
			DOM::append($section, $value);
		DOM::append($section, $row);
	DOM::append($sideCol, $section);
	$section = DOM::create('div', '', '', 'window-module');
	DOM::append($sideCol, $section);
DOM::append($container, $sideCol);

// return
return  $wFrame->getFrame();
//#section_end#
?>