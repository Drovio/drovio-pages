<?php
//#section#[header]
// Module Declaration
$moduleID = 102;

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
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
// Clear report stack
report::clear();



//_____ Place Your Code Here
// Returns the development page notification
$default = reporter::get("success", "info", "info.page_default");



// Add content
report::add_content($default, $data_holder = NULL, $method = "replace", $prompt = FALSE);

// Return the report
return report::get();
//#section_end#
?>