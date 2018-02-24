<?php
//#section#[header]
// Module Declaration
$moduleID = 88;

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
use \API\Resources\literals\literal;
use \API\Resources\url;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "bossFrontend");

// Global page container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("mainContent", $globalContainer);

// Left container
$infoContainer = DOM::create("div", "", "", "infoContainer".($loggedIn ? " cntr" : ""));
DOM::append($globalContainer, $infoContainer);

// Logo
$logoDiv = DOM::create("div", "", "", "logoContainer");
DOM::append($infoContainer, $logoDiv);

// Subtitle
$subContent = moduleLiteral::get($moduleID, "lbl_pageTitle");
$subTitle = DOM::create("h1", "", "", "infoTitle");
DOM::append($subTitle, $subContent);
DOM::append($infoContainer, $subTitle);

// Create service container
$pageContainer = DOM::create("div", "", "", "pageContainer");
DOM::append($globalContainer, $pageContainer);

return $page->getReport();
//#section_end#
?>