<?php
//#section#[header]
// Module Declaration
$moduleID = 31;

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
importer::useCore('resources::multilingual_center');
importer::useCore('builders::prototype::model::module.receptor.model');
importer::useCore('resources::geolocation_center');
importer::useAPI('appLoader');
importer::useCore('builders::prototype::page.prototype');
importer::useCore('communications::dbqLoader');
importer::useCore('navigation::globalNavigation');


// Returns the development page notification
return $sys_rep->page_code_default();
//#section_end#
?>