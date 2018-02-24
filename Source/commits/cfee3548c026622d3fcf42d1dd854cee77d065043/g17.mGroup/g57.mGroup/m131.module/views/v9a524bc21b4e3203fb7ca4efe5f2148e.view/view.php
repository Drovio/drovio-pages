<?php
//#section#[header]
// Module Declaration
$moduleID = 131;

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
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;

$templateID = $_GET['id'];

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
//$globalContainer = $HTMLContentBuilder->build()->get();

$info = DOM::create("div");
//DOM::append($globalContainer, $info);
$HTMLContentBuilder->buildElement($info);
$infoArray = template::getTemplateInfo($templateID);

// Title
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateTitle");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['templateTitle']);
DOM::append($wrapper, $text);

// templateType
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateType");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['templateType']);
DOM::append($wrapper, $text);

// templateDescription
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateDescription");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['templateDescription']);
DOM::append($wrapper, $text);

// groupTitle
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateGroupTitle");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['groupTitle']);
DOM::append($wrapper, $text);

// groupDescription
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateGroupDescription");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['groupDescription']);
DOM::append($wrapper, $text);


// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>