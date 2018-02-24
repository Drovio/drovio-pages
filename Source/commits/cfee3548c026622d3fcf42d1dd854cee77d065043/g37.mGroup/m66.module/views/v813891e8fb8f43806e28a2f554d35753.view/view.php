<?php
//#section#[header]
// Module Declaration
$moduleID = 66;

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
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \UI\Forms\formReport\formNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	print_r($_POST);
	
	// Authenticate
	
	// Return Report
	$notification = new formNotification();
	$notification->build("success");
	
	$content = DOM::create("div", "Testing Report");
	$notification->append($content);
	return $notification->getReport($reset = TRUE);
}
//#section_end#
?>