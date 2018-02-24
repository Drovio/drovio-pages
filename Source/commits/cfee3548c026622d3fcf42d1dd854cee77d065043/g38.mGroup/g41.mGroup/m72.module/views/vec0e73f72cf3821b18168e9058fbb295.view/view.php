<?php
//#section#[header]
// Module Declaration
$moduleID = 72;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral; 
//use \API\Content\literals\literal;
//use \API\Platform\state\url;
use \API\Resources\documentParser;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\notification;

$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "privacyHomeModule");

$documentParser = new documentParser();
$documentParser->setLocale('el_GR');
$document = $documentParser->load("Legal::Privacy::index.html", $text = FALSE);

$docElem = DOM::create('div', DOM::import($document));
//DOM::append($docElem, $document);
$page->appendToSection("mainContent", $docElem);

/*
$moduleHeader = DOM::create("h1");
$page->appendToSection("mainContent", $moduleHeader);
$title = moduleLiteral::get($moduleID, "title");
DOM::append($moduleHeader, $title);

$success_notification = new notification();
$success_notification->build($type = "warning", $header = TRUE, $footer = TRUE);
$success_message = $success_notification->getMessage("warning", "wrn.content_uc");
$success_notification->append($success_message);

$success_registration = $success_notification->get();
$page->appendToSection("mainContent", $success_registration);
*/

return $page->getReport();
//#section_end#
?>