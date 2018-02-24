<?php
//#section#[header]
// Module Declaration
$moduleID = 159;

// Inner Module Codes
$innerModules = array();
$innerModules['generalSettings'] = 160;
$innerModules['securitySettings'] = 161;
$innerModules['keySettings'] = 317;
$innerModules['managedAccounts'] = 328;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Literals");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "mySettingsPage", TRUE);

// Set menu actions
$menuItem = HTML::select(".mySettingsPage .side_menu .menu_item.general")->item(0);
$actionFactory->setModuleAction($menuItem, $innerModules['generalSettings'], "", ".settingsContainer", array(), $loading = TRUE);
NavigatorProtocol::staticNav($menuItem, "", "", "sideNavGroup", $display = "none");

$menuItem = HTML::select(".mySettingsPage .side_menu .menu_item.security")->item(0);
$actionFactory->setModuleAction($menuItem, $innerModules['securitySettings'], "", ".settingsContainer", array(), $loading = TRUE);
NavigatorProtocol::staticNav($menuItem, "", "", "sideNavGroup", $display = "none");

$menuItem = HTML::select(".mySettingsPage .side_menu .menu_item.managed")->item(0);
$actionFactory->setModuleAction($menuItem, $innerModules['managedAccounts'], "", ".settingsContainer", array(), $loading = TRUE);
NavigatorProtocol::staticNav($menuItem, "", "", "sideNavGroup", $display = "none");

$menuItem = HTML::select(".mySettingsPage .side_menu .menu_item.keys")->item(0);
$actionFactory->setModuleAction($menuItem, $innerModules['keySettings'], "", ".settingsContainer", array(), $loading = TRUE);
NavigatorProtocol::staticNav($menuItem, "", "", "sideNavGroup", $display = "none");


// Initialize with general settings
$settingsContainer = HTML::select(".settingsContainer")->item(0);
$personalContainer = $page->getModuleContainer($innerModules['generalSettings'], $action = "", $attr = array(), $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
DOM::append($settingsContainer, $personalContainer);


return $page->getReport();
//#section_end#
?>