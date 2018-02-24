<?php
//#section#[header]
// Module Declaration
$moduleID = 158;

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
importer::import("ESS", "Protocol");
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
$page->build($pageTitle, "developerHomePage");


$welcomeTitle = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = DOM::create("h2");
DOM::append($header, $welcomeTitle);
$page->appendToSection("mainContent", $header);

layoutContainer::textAlign($header, $align = "center");
layoutContainer::padding($header, $orientation = "t", $size = "l");

// Create developer's menu
$gridView = new gridView();
$gridViewElement = $gridView->build(3, 4)->get();
layoutContainer::padding($gridViewElement, $orientation = "", $size = "l");
$page->appendToSection("mainContent", $gridViewElement);


// Set Menu Content
//_____ SDK Programming
$title = moduleLiteral::get($moduleID, "lbl_coreProgramming");
$content = weblink::get(url::resolve("admin", "/developer/sdk/"), "_self", $title);
$gridView->append(0, 0, $content);

//_____ Module Programming
$title = moduleLiteral::get($moduleID, "lbl_moduleProgramming");
$content = weblink::get(url::resolve("admin", "/developer/modules/"), "_self", $title);
$gridView->append(0, 1, $content);

//_____ Widget Programming
$title = moduleLiteral::get($moduleID, "lbl_widgetProgramming");
$content = weblink::get(url::resolve("admin", "/developer/widgets/"), "_self", $title);
$gridView->append(0, 2, $content);

//_____ ajax Programming
$title = moduleLiteral::get($moduleID, "lbl_ajaxProgramming");
$content = weblink::get(url::resolve("admin", "/developer/ajax/"), "_self", $title);
$gridView->append(0, 3, $content);

//_____ Database Manager
$title = moduleLiteral::get($moduleID, "lbl_databaseManager");
$content = weblink::get(url::resolve("admin", "/developer/database/"), "_self", $title);
$gridView->append(1, 0, $content);

//_____ appcenter Core Programming
$title = moduleLiteral::get($moduleID, "lbl_appCenterProgramming");
$content = weblink::get(url::resolve("admin", "/developer/appcenter/"), "_self", $title);
$gridView->append(1, 1, $content);

//_____ ebuilder Core Programming
$title = moduleLiteral::get($moduleID, "lbl_ebuilderProgramming");
$content = weblink::get(url::resolve("admin", "/developer/ebuilder/"), "_self", $title);
$gridView->append(1, 2, $content);

//_____ Resource Manager
$title = moduleLiteral::get($moduleID, "lbl_resourceManager");
$content = weblink::get(url::resolve("admin", "/developer/resources/"), "_self", $title);
$gridView->append(1, 3, $content);

//_____ Admin Developer Docs
$title = moduleLiteral::get($moduleID, "lbl_adminDevDocs");
$content = weblink::get(url::resolve("admin", "/developer/docs/"), "_self", $title);
$gridView->append(2, 0, $content);

// Return output
return $page->getReport();
//#section_end#
?>