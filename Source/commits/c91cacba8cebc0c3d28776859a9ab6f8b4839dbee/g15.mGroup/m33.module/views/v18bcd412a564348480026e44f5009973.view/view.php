<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['testerHomePage'] = 105;
$innerModules['pageManager'] = 249;
$innerModules['securityManager'] = 97;
$innerModules['resourcesPage'] = 57;
$innerModules['publisher'] = 113;
$innerModules['reportsPage'] = 179;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Modules");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;

// Module Content
$pageContent = new MContent($moduleID);
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

// Page Manager
$header = moduleLiteral::get($moduleID, "tlb_homeNav_pages");
$url = url::resolve("admin", $url = "/pages/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['pageManager'], $url);
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