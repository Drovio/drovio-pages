<?php
//#section#[header]
// Module Declaration
$moduleID = 57;

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
importer::import("ESS", "Prototype");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\url;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\htmlComponents\weblink;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\gridView;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page->build($pageTitle, "adminHomePage");


$welcomeTitle = moduleLiteral::get($moduleID, "pageTitle");
$header = DOM::create("h2");
DOM::append($header, $welcomeTitle);
$page->appendToSection("mainContent", $header);

layoutContainer::textAlign($header, $align = "center");
layoutContainer::padding($header, $orientation = "t", $size = "l");

// Create developer's menu
$gridView = new gridView();
$gridViewElement = $gridView->build(2, 4)->get();
layoutContainer::padding($gridViewElement, $orientation = "", $size = "l");
$page->appendToSection("mainContent", $gridViewElement);


// Set Menu Content
//_____ literals Manager
$title = moduleLiteral::get($moduleID, "lbl_literalsManager");
$content = weblink::get(url::resolve("admin", "/resources/literals/"), "_self", $title);
$gridView->append(0, 0, $content);

//_____ layouts Manager
$title = moduleLiteral::get($moduleID, "lbl_layoutsManager");
$content = weblink::get(url::resolve("admin", "/resources/layouts/"), "_self", $title);
$gridView->append(0, 1, $content);

//_____ media Manager
$title = moduleLiteral::get($moduleID, "lbl_mediaManager");
$content = weblink::get(url::resolve("admin", "/resources/media/"), "_self", $title);
$gridView->append(0, 2, $content);

//_____ sdk Manager
$title = moduleLiteral::get($moduleID, "lbl_sdkManager");
$content = weblink::get(url::resolve("admin", "/resources/sdk/"), "_self", $title);
$gridView->append(0, 3, $content);

//_____ schemas Manager
$title = moduleLiteral::get($moduleID, "lbl_schemasManager");
$content = weblink::get(url::resolve("admin", "/resources/schemas/"), "_self", $title);
$gridView->append(1, 0, $content);

//_____ geoloc Manager
$title = moduleLiteral::get($moduleID, "lbl_geolocManager");
$content = weblink::get(url::resolve("admin", "/resources/geoloc/"), "_self", $title);
$gridView->append(1, 1, $content);


// Return output
return $page->getReport();
//#section_end#
?>