<?php
//#section#[header]
// Module Declaration
$moduleID = 136;

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
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Developer\profiler\tester;
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;

// Create Page
$page = new HTMLModulePage("OneColumnFullscreen");
$page->build("My Testing Trunk", "trunkSite");

$explorer = new fileExplorer(tester::getTrunk(), "testerTrunk", "My Trunk");

$container = $explorer->build()->get();
$page->appendToSection("mainContent", $container);

return $page->getReport();
//#section_end#
?>