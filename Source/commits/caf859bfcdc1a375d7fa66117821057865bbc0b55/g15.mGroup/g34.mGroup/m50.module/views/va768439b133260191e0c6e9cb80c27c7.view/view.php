<?php
//#section#[header]
// Module Declaration
$moduleID = 50;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\url;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\gridView;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page->build($pageTitle, "securityPage");


$welcomeTitle = moduleLiteral::get($moduleID, "pageTitle");
$header = DOM::create("h2");
DOM::append($header, $welcomeTitle);
$page->appendToSection("mainContent", $header);

layoutContainer::textAlign($header, $align = "center");
layoutContainer::padding($header, $orientation = "t", $size = "l");

// Create developer's menu
$gridView = new gridView();
$gridViewElement = $gridView->build(1, 3)->get();
layoutContainer::padding($gridViewElement, $orientation = "", $size = "l");
$page->appendToSection("mainContent", $gridViewElement);


// Set Menu Content
//_____ literals Manager
$content = moduleLiteral::get($moduleID, "lbl_moduleManager");
$link = $page->getWeblink(url::resolve("admin", "/security/modules/"), $content, "_self", "", "");
$gridView->append(0, 0, $link);

//_____ layouts Manager
$content = moduleLiteral::get($moduleID, "lbl_userGroups");
$link = $page->getWeblink(url::resolve("admin", "/security/groups/"), $content, "_self", "", "");
$gridView->append(0, 1, $link);

//_____ media Manager
$content = moduleLiteral::get($moduleID, "lbl_privileges");
$link = $page->getWeblink(url::resolve("admin", "/security/privileges/"), $content, "_self", "" ,"");
$gridView->append(0, 2, $link);


// Return output
return $page->getReport();
//#section_end#
?>