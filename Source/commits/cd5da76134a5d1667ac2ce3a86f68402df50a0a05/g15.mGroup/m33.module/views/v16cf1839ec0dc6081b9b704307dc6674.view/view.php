<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['securityHome'] = 50;
$innerModules['moduleManager'] = 97;
$innerModules['accountGroups'] = 90;
$innerModules['privileges'] = 163;

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
$pageContent->build("toolbarSecurityNav", "adminToolbarNav");

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

// Security Home
$header = moduleLiteral::get($moduleID, "tlb_securityNat_home");
$url = url::resolve("admin", $url = "/security/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['securityHome'], $url);
DOM::append($navMenu, $item);

// Module Manager
$header = moduleLiteral::get($moduleID, "tlb_securityNat_modules");
$url = url::resolve("admin", $url = "/security/modules/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['moduleManager'], $url);
DOM::append($navMenu, $item);

// Account Groups
$header = moduleLiteral::get($moduleID, "tlb_securityNat_userGroups");
$url = url::resolve("admin", $url = "/security/groups/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['accountGroups'], $url);
DOM::append($navMenu, $item);

// Privileges
$header = moduleLiteral::get($moduleID, "tlb_securityNat_privileges");
$url = url::resolve("admin", $url = "/security/privileges/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['privileges'], $url);
DOM::append($navMenu, $item);

// Return output
return $pageContent->getReport();
//#section_end#
?>