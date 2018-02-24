<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Platform");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
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
$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "adminHomePage");


$welcomeTitle = moduleLiteral::get($moduleID, "lbl_pageTitle");
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
//_____ SDKProgramming
$title = moduleLiteral::get($moduleID, "lbl_developerSite");
$content = weblink::get(url::resolve("admin", "/developer/"), "_self", $title);
$gridView->append(0, 0, $content);

//_____ Module Programming
$title = moduleLiteral::get($moduleID, "lbl_testerSite");
$content = weblink::get(url::resolve("admin", "/tester/"), "_self", $title);
$gridView->append(0, 1, $content);

//_____ Database Manager
$title = moduleLiteral::get($moduleID, "lbl_pageManager");
$content = weblink::get(url::resolve("admin", "/pages/"), "_self", $title);
$gridView->append(0, 2, $content);

//_____ privileges Manager
$title = moduleLiteral::get($moduleID, "lbl_settingsManager");
$content = weblink::get(url::resolve("admin", "/config/"), "_self", $title);
$gridView->append(0, 3, $content);

//_____ appcenter Programming
$title = moduleLiteral::get($moduleID, "lbl_securityManager");
$content = weblink::get(url::resolve("admin", "/security/"), "_self", $title);
$gridView->append(1, 0, $content);

//_____ resources Manager
$title = moduleLiteral::get($moduleID, "lbl_resources");
$content = weblink::get(url::resolve("admin", "/resources/"), "_self", $title);
$gridView->append(1, 1, $content);

//_____ System Deploy and Publish Manager
$title = moduleLiteral::get($moduleID, "lbl_publisherSite");
$content = weblink::get(url::resolve("admin", "/publisher/"), "_self", $title);
$gridView->append(1, 2, $content);

//_____ System Monitoring And Reporting
$title = moduleLiteral::get($moduleID, "lbl_reporting");
$content = weblink::get(url::resolve("admin", "/reporting/"), "_self", $title);
$gridView->append(1, 2, $content);

// Return output
return $page->getReport();
//#section_end#
?>