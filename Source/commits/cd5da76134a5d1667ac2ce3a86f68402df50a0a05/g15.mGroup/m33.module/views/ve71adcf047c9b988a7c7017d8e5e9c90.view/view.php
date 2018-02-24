<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['rsrcHome'] = 57;
$innerModules['rsrcLiterals'] = 93;
$innerModules['mediaManager'] = 112;
$innerModules['geolocation'] = 144;
$innerModules['sdkResources'] = 118;
$innerModules['schemas'] = 119;

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
$pageContent->build("toolbarResourcesNav", "adminToolbarNav");

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

// Resources Home
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_home");
$url = url::resolve("admin", $url = "/resources/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['rsrcHome'], $url);
DOM::append($navMenu, $item);

// Literal Manager
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_literals");
$url = url::resolve("admin", $url = "/resources/literals/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['rsrcLiterals'], $url);
DOM::append($navMenu, $item);

// Media Management
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_media");
$url = url::resolve("admin", $url = "/resources/media/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['mediaManager'], $url);
DOM::append($navMenu, $item);

// Geolocation
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_geoloc");
$url = url::resolve("admin", $url = "/resources/geoloc/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['geolocation'], $url);
DOM::append($navMenu, $item);

// SDK Resources
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_sdk");
$url = url::resolve("admin", $url = "/resources/sdk/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['sdkResources'], $url);
DOM::append($navMenu, $item);

// Schemas
$header = moduleLiteral::get($moduleID, "tlb_rsrcNav_schemas");
$url = url::resolve("admin", $url = "/resources/schemas/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['schemas'], $url);
DOM::append($navMenu, $item);

// Return output
return $pageContent->getReport();
//#section_end#
?>