<?php
//#section#[header]
// Module Declaration
$moduleID = 73;

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
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\Url;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "legalPage");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageHeader");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_pageSubtitle");
$header = DOM::create("h3", $title);
$page->appendToSection("mainContent", $header);


// Terms of Service
$title = DOM::create("a", moduleLiteral::get($moduleID, "lbl_readTerms"));
$url = Url::resolve("support", "/legal/terms/");
DOM::attr($title, "href", $url);
DOM::attr($title, "target", "_self");
$header = DOM::create("h4", $title);
$page->appendToSection("mainContent", $header);

// Data Use Policy
$title = DOM::create("a", moduleLiteral::get($moduleID, "lbl_readDataPolicy"));
$url = Url::resolve("support", "/about/privacy/");
DOM::attr($title, "href", $url);
DOM::attr($title, "target", "_self");
$header = DOM::create("h4", $title);
$page->appendToSection("mainContent", $header);

return $page->getReport();
//#section_end#
?>