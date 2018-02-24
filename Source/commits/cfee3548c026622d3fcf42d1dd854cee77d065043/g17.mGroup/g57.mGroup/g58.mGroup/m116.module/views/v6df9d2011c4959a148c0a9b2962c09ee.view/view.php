<?php
//#section#[header]
// Module Declaration
$moduleID = 116;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\formControls\formItem;
use \UI\Html\HTMLContent;

$HTMLContent = new HTMLContent();
$HTMLContent->build();

$container = $HTMLContent->get();

// # Release
$header = DOM::create("h2", "", "", "sectionHeader");
$headerContent = moduleLiteral::get($moduleID, "lbl_releaseSecHdr");
DOM::append($header, $headerContent);
DOM::append($container, $header);
		
// Create Release Button
$formItem = new formItem();
$formItem->build("button", $name, $id, "", "uiFormButton".($positive ? " positive" : ""));
$brn_createGroup = $formItem->get(); 
$title = DOM::create('span',"Release");//moduleLiteral::get($moduleID, "lbl_createUserGroup");
DOM::append($brn_createGroup, $title);
$attr = $defAttr;
//$actionFactory->setModuleAction($brn_createGroup, $innerModules['templateObject'], "publishTemplate", "", $attr);
DOM::append($container , $brn_createGroup);
			


// Return output
return $HTMLContent->getReport();
//#section_end#
?>