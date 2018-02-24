<?php
//#section#[header]
// Module Declaration
$moduleID = 195;

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
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage("simpleOneColumnCenter");

// Build the module
$page->build("Under Construction", "uc");


//_____ Place Your Code Here
// Create the under development notification
$udNotification = reporter::get("success", "info", "info.page_default");
$page->appendToSection("mainContent", $udNotification);



// Return output
return $page->getReport();
//#section_end#
?>