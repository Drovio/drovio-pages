<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['devHomePage'] = on;
$innerModules['testerHomePage'] = 105;
$innerModules['pageManager'] = 68;
$innerModules['securityManager'] = 97;
$innerModules['resourcesPage'] = 57;
$innerModules['publisher'] = 113;
$innerModules['reportsPage'] = 179;

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
use \UI\Html\HTMLContent;

// Module Content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarHomeNav", "adminToolbarNav");

// Build the menu
$navMenu = DOM::create("ul", "", "", "navMenu");
$pageContent->append($navMenu);

// Create toolbar nav item
function getNavMenuItem($header, $actionFactory, $moduleID, $href)
{
	// Create item
	$item = DOM::create("li", "", "", "navMenuItem");
	
	$itemContent = DOM::create("a", $header);
	DOM::attr($itemContent, "href", $href);
	DOM::attr($itemContent, "target", "_self");
	DOM::append($item, $itemContent);
	if (!is_null($moduleID))
		$actionFactory->setModuleAction($itemContent, $moduleID);
	
	return $item;
}

// Admin Home
$header = moduleLiteral::get($moduleID, "lbl_homeNav_header");
$url = url::resolve("admin", $url = "/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $moduleID, $url);
DOM::append($navMenu, $item);

// Tester Home
$header = moduleLiteral::get($moduleID, "tlb_homeNav_tester");
$url = url::resolve("admin", $url = "/tester/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['testerHomePage'], $url);
DOM::append($navMenu, $item);

// Page Manager
$header = moduleLiteral::get($moduleID, "tlb_homeNav_pages");
$url = url::resolve("admin", $url = "/pages/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['pageManager'], $url);
DOM::append($navMenu, $item);

// Settings Manager
$header = moduleLiteral::get($moduleID, "tlb_homeNav_settings");
$url = url::resolve("admin", $url = "/config/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, NULL, $url);//$innerModules['pageManager'], $url);
DOM::append($navMenu, $item);

// Admin Reporting
$header = moduleLiteral::get($moduleID, "tlb_homeNav_reporting");
$url = url::resolve("admin", $url = "/reporting/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['reportsPage'], $url);
DOM::append($navMenu, $item);

// Admin Publisher
$header = moduleLiteral::get($moduleID, "tlb_homeNav_publisher");
$url = url::resolve("admin", $url = "/publisher/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['publisher'], $url);
DOM::append($navMenu, $item);

// Return output
return $pageContent->getReport();
//#section_end#
?>