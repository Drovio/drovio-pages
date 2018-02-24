<?php
//#section#[header]
// Module Declaration
$moduleID = 48;

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
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;

$content = new HTMLContent();
$content->build("messageCenterSnippet", "messageWidget");

$title = DOM::create("h3", "Message Center");
$content->append($title);

return $content->getReport();
//#section_end#
?>