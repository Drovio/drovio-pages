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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Profile");
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Profile\tester;
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;

// Create Page
$page = new HTMLModulePage("OneColumnFullscreen");
$page->build("", "testerTrunk");

// Build fileExplorer for tester trunk
$explorer = new fileExplorer(tester::getTrunk(), "testerTrunk", "My Trunk");
$container = $explorer->build()->get();
$page->appendToSection("mainContent", $container);

// Return report
return $page->getReport("", FALSE);
//#section_end#
?>