<?php
//#section#[header]
// Module Declaration
$moduleID = 105;

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
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;

// Get POST Variables
$moduleID = $_POST['moduleParent'];

// Build Content
$content = new HTMLContent();

// Create module Container
$container = $content->getModuleContainer($moduleID, $action = "", $attr = array(), $startup = TRUE);
$content->buildElement($container);

// Return report
return $content->getReport("#testingContainer");
//#section_end#
?>