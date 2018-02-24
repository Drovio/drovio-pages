<?php
//#section#[header]
// Module Declaration
$moduleID = 98;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;

$page = new HTMLModulePage("OneColumnCentered");
$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "myServicesPage");

// Title
$welcomeTitle = moduleLiteral::get($moduleID, "lbl_welcomeTitle");
$header = DOM::create("h2", "", "", "pageHeader");
DOM::append($header, $welcomeTitle);
$page->appendToSection("mainContent", $header);

// Content
$servicesMessage = moduleLiteral::get($moduleID, "lbl_pageSubTitle");
$messageP = DOM::create("p");
DOM::append($messageP, $servicesMessage);
$page->appendToSection("mainContent", $messageP);


return $page->getReport();
//#section_end#
?>