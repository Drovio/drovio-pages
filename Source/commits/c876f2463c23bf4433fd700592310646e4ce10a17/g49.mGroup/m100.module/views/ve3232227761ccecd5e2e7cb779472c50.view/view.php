<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPage'] = 66;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("freeLayout");
$page->build($pageTitle, "devCenterPage");

// Global page container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("main", $globalContainer);

// Left container
$infoContainer = DOM::create("div", "", "", "infoContainer cntr");
DOM::append($globalContainer, $infoContainer);

// Logo
$logoDiv = DOM::create("div", "", "", "logoContainer");
DOM::append($infoContainer, $logoDiv);

// Subtitle
$subContent = moduleLiteral::get($moduleID, "msg_headMessage");
$subTitle = DOM::create("h1", "", "", "infoTitle");
DOM::append($subTitle, $subContent);
DOM::append($infoContainer, $subTitle);


// Info Bullets
$bullets = DOM::create("ul", "", "", "devPrograms");
DOM::append($infoContainer, $bullets);

// Developer Profile Page
$title = moduleLiteral::get($moduleID, "lbl_devProfile");
$bulletUrl = Url::resolve("developer", "/profile/");
$bulletA = $page->getWeblink($bulletUrl, $title, $target = "_blank");
$bulletLi = DOM::create("li", $bulletA);
DOM::append($bullets, $bulletLi);

// Developer Docs
$title = moduleLiteral::get($moduleID, "lbl_documentation");
$bulletUrl = Url::resolve("developer", "/docs/");
$bulletA = $page->getWeblink($bulletUrl, $title, $target = "_blank");
$bulletLi = DOM::create("li", $bulletA);
DOM::append($bullets, $bulletLi);

// Bugs
$title = moduleLiteral::get($moduleID, "lbl_bugReporting");
$bulletUrl = Url::resolve("www", "/help/");
$bulletA = $page->getWeblink($bulletUrl, $title, $target = "_blank");
$bulletLi = DOM::create("li", $bulletA);
DOM::append($bullets, $bulletLi);

return $page->getReport();
//#section_end#
?>