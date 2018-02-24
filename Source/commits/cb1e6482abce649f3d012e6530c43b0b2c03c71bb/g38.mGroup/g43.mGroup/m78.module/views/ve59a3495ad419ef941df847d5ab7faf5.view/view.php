<?php
//#section#[header]
// Module Declaration
$moduleID = 78;

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
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "helpCenter");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_pageSubtitle");
$header = DOM::create("h3", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_contactPage");
$webC = DOM::create("a", $title);
$url = url::resolve("www", "/help/contact/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($servicesContainer, $header);
$page->appendToSection("mainContent", $header);

return $page->getReport();
//#section_end#
?>