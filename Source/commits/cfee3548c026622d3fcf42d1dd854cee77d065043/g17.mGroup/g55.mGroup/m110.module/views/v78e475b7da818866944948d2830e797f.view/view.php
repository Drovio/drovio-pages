<?php
//#section#[header]
// Module Declaration
$moduleID = 110;

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
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\resources\layouts\systemLayout;
use \API\Developer\resources\layouts\ebuilderLayout;
use \INU\Developer\redWIDE;
use \API\Developer\content\document\parsers\phpParser;

$group = $_POST['group'];
$layoutName = $_POST['name'];

switch($group)
{
	case 'ebuilder' :
		$layoutManager = new ebuilderLayout($layoutName);
		break;
	case 'system' :
		$layoutManager = new systemLayout($layoutName);
		break;
	default :
		break;	
}

// Get Source Code
$code = $_POST['wideContent'];

// Update Source Code
$status = $layoutManager->saveStructure($code);


//---------- AUTO-GENERATED CODE ----------//
// Clear report stack
report::clear();

// redWIDE status report
report::addContent(Reporter::statusReport($status), "popup");
return report::get();
//#section_end#
?>