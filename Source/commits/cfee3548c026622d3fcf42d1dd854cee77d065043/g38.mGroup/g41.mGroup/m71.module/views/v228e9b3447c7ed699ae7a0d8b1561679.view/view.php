<?php
//#section#[header]
// Module Declaration
$moduleID = 71;

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
importer::import("API", "Content");
importer::import("API", "Model");
importer::import("API", "Platform");
importer::import("UI", "Notifications");
//#section_end#
//#section#[code]
use \API\Content\literals\moduleLiteral;
use \API\Content\literals\literal;
use \API\Platform\state\url;
use \API\Content\resources\documentParser;
use \API\Model\layout\components\modulePage;
use \UI\Notifications\notification;

$pageTitle = moduleLiteral::get($policyCode, "title", FALSE);
$mdlPage = new modulePage($pageTitle, "simpleOneColumnCenter");

$document = documentParser::load("Frontend::about.html", $text = FALSE);
//$mdlPage->append_to_section("mainContent", $document);

$moduleHeader = DOM::create("h1");
$mdlPage->append_to_section("mainContent", $moduleHeader);

$title = moduleLiteral::get($moduleID, "title");
DOM::append($moduleHeader, $title);

$success_notification = new notification();
$success_notification->build_notification($type = "warning", $header = TRUE, $footer = TRUE);
$success_message = $success_notification->get_message("warning", "wrn.content_uc");
$success_notification->append_content($success_message);

$success_registration = $success_notification->get_notification();
$mdlPage->append_to_section("mainContent", $success_registration);

report::clear();
report::add_content($mdlPage->get_page_body());
return report::get();
//#section_end#
?>