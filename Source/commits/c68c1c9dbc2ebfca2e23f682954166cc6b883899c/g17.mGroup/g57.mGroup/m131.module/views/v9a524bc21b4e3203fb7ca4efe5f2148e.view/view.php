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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;

$templateID = $_GET['id'];

// Try to load Object
$templateManager = new templateManager();
$infoArray = $templateManager->getTemplateInfo($templateID);

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
//$globalContainer = $HTMLContentBuilder->build()->get();

$info = DOM::create("div");
//DOM::append($globalContainer, $info);
$HTMLContentBuilder->buildElement($info);

// name
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateName");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['name']);
DOM::append($wrapper, $text);


// Title
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateTitle");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['title']);
DOM::append($wrapper, $text);

// templateType
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateType");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['projectType']);
DOM::append($wrapper, $text);

// templateDescription
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateDescription");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['description']);
DOM::append($wrapper, $text);

// groupTitle
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateGroupTitle");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['projectCategory']);
DOM::append($wrapper, $text);

// groupTitle
$wrapper = DOM::create("div");
DOM::append($info, $wrapper);
$label = moduleLiteral::get($moduleID, "lbl_templateStatus");
DOM::append($wrapper, $label);
$colonSeperator = DOM::create("span"," : ");
DOM::append($wrapper, $colonSeperator);
$text = DOM::create("span", $infoArray['projectStatus']);
DOM::append($wrapper, $text);


// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>