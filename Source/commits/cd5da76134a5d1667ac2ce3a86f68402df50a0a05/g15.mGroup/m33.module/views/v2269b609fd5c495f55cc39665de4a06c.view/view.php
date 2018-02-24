<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['devHomePage'] = 158;
$innerModules['sdkDeveloper'] = 56;
$innerModules['ajaxDeveloper'] = 95;
$innerModules['databaseDeveloper'] = 49;
$innerModules['moduleDeveloper'] = 64;
$innerModules['welDeveloper'] = 121;
$innerModules['aelDeveloper'] = 125;
$innerModules['devResources'] = 124;
$innerModules['devDocs'] = 99;
$innerModules['devHome'] = 158;

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
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLContent;

// Create Content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "adminToolbarNav");

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

// Developer Home
$header = moduleLiteral::get($moduleID, "tlb_devNav_home");
$url = url::resolve("admin", $url = "/developer/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['devHome'], $url);
DOM::append($navMenu, $item);

// SDK Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_sdk");
$url = url::resolve("admin", $url = "/developer/sdk/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['sdkDeveloper'], $url);
DOM::append($navMenu, $item);

// Ajax Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_ajax");
$url = url::resolve("admin", $url = "/developer/ajax/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['ajaxDeveloper'], $url);
DOM::append($navMenu, $item);

// SQL Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_database");
$url = url::resolve("admin", $url = "/developer/database/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['databaseDeveloper'], $url);
DOM::append($navMenu, $item);

// Module Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_modules");
$url = url::resolve("admin", $url = "/developer/modules/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['moduleDeveloper'], $url);
DOM::append($navMenu, $item);

// Web Engine Library SDK Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_wel");
$url = url::resolve("admin", $url = "/developer/ebuilder/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['welDeveloper'], $url);
DOM::append($navMenu, $item);

// App Engine Library SDK Developer
$header = moduleLiteral::get($moduleID, "tlb_devNav_ael");
$url = url::resolve("admin", $url = "/developer/appcenter/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['aelDeveloper'], $url);
DOM::append($navMenu, $item);

// Developer Resources
$header = moduleLiteral::get($moduleID, "tlb_devNav_resources");
$url = url::resolve("admin", $url = "/developer/resources/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['devResources'], $url);
DOM::append($navMenu, $item);

// Developer Docs
$header = moduleLiteral::get($moduleID, "tlb_devNav_docs");
$url = url::resolve("admin", $url = "/developer/docs/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['devDocs'], $url);
DOM::append($navMenu, $item);

// Return output
return $pageContent->getReport();
//#section_end#
?>