<?php
//#section#[header]
// Module Declaration
$moduleID = 99;

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
use \API\Resources\url;
use \UI\Html\HTMLModulePage;


// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "devPrograms");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageHeader");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_pageSubtitle");
$header = DOM::create("h3", $title);
$page->appendToSection("mainContent", $header);


// Programs
$pContainer = DOM::create("div", "", "", "programContainer");
$page->appendToSection("mainContent",$pContainer);

// Application Developer
$title = DOM::create("label", moduleLiteral::get($moduleID, "lbl_appDeveloper"));
$program = DOM::create("a", $title, "", "program".($appDeveloper ? " active" : ""));
DOM::append($pContainer, $program);

$url = url::resolve("developer", "/docs/appCenter/");
DOM::attr($program, "href", $url);
DOM::attr($program, "target", "_blank");

// Website Developer
$title = DOM::create("label", moduleLiteral::get($moduleID, "lbl_wDeveloper"));
$program = DOM::create("a", $title, "", "program".($wDeveloper ? " active" : ""));
DOM::append($pContainer, $program);

$url = url::resolve("developer", "/docs/ebuilder/");
DOM::attr($program, "href", $url);
DOM::attr($program, "target", "_blank");

return $page->getReport();
//#section_end#
?>