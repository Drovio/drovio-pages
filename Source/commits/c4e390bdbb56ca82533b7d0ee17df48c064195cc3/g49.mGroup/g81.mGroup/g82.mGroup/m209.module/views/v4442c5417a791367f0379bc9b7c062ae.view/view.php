<?php
//#section#[header]
// Module Declaration
$moduleID = 209;

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
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \UI\Interactive\forms\switchButton;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get status
	$status = (logger::status() == FALSE);
	
	// Activate or deactivate
	if (logger::status() == FALSE)
		logger::activate();
	else
		logger::deactivate();
	
	// Return report status
	return switchButton::getReport($status);
}
return FALSE;
//#section_end#
?>