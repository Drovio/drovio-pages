<?php
//#section#[header]
// Module Declaration
$moduleID = 76;

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
$page->build($pageTitle, "supportPage");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageHeader");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_pageSubtitle");
$header = DOM::create("h3", $title);
$page->appendToSection("mainContent", $header);

function getTile($header, $sub, $href)
{
	$tile = DOM::create("div", "", "", "tile");
	$tileNav = DOM::create("a");
	DOM::attr($tileNav, "href", $href);
	DOM::attr($tileNav, "target", "_self");
	DOM::append($tile, $tileNav);
	$header = DOM::create("h2", $header);
	DOM::append($tileNav, $header);
	$sub = DOM::create("p", $sub);
	DOM::append($tileNav, $sub);
	
	return $tile;
}

// Build tiles
$tileContainer = DOM::create("div", "", "", "gContainer");
$page->appendToSection("mainContent", $tileContainer);

// Legal Tile
$header = moduleLiteral::get($moduleID, "lbl_legalHeader");
$sub = moduleLiteral::get($moduleID, "lbl_legalSub");
$url = Url::resolve("www", "/help/legal/");
$tile = getTile($header, $sub, $url);
DOM::append($tileContainer, $tile);

// Contact Tile
$header = moduleLiteral::get($moduleID, "lbl_contactHeader");
$sub = moduleLiteral::get($moduleID, "lbl_contactSub");
$url = Url::resolve("www", "/help/contact/");
$tile = getTile($header, $sub, $url);
DOM::append($tileContainer, $tile);

// Feedback Tile
$header = moduleLiteral::get($moduleID, "lbl_bugHeader");
$sub = moduleLiteral::get($moduleID, "lbl_bugSub");
$url = Url::resolve("www", "/help/feedback/");
$tile = getTile($header, $sub, $url);
DOM::append($tileContainer, $tile);

return $page->getReport();
//#section_end#
?>