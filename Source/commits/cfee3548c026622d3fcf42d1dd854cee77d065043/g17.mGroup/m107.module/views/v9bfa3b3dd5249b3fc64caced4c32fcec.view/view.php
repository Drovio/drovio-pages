<?php
//#section#[header]
// Module Declaration
$moduleID = 107;

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
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \API\Resources\layoutManager;

$global = DOM::create('div', '','global');

//$status = layoutManager::export();

$code= DOM::create('span', $status);
DOM::append($global, $code);


//Return Report
report::clear();
report::add_content($global);
return report::get();
//#section_end#
?>