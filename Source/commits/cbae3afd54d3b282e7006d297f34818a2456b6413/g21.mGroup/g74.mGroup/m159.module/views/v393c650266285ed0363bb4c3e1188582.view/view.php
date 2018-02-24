<?php
//#section#[header]
// Module Declaration
$moduleID = 159;

// Inner Module Codes
$innerModules = array();
$innerModules['generalSettings'] = 160;
$innerModules['securitySettings'] = 161;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Navigation\sideMenu;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "mySettingsPage", TRUE);

// Get Settings section
$section = $_GET['section'];


$pageSideBar = HTML::select(".uiMainSideNav")->item(0);
// Create sidemenu
$menu = new sideMenu();
$sideMenu = $menu->build("settingsMenu")->get();
DOM::append($pageSideBar, $sideMenu);

// Navigation Attributes
$navGroup = "settingsNav";
$navDisplay = "none";

// General Settings
$title = moduleLiteral::get($moduleID, "lbl_generalSettings");
$menuItem = $menu->insertListItem($id = "generalSettings", $title);
$menu->addNavigation($menuItem, "", "", "", $navGroup, $navDisplay);
if (empty($section))
	DOM::appendAttr($menuItem, "class", "selected");
$actionFactory->setModuleAction($menuItem, $innerModules['generalSettings'], "", ".settingsContainer");

// Security Settings
$title = moduleLiteral::get($moduleID, "lbl_securitySettings");
$menuItem = $menu->insertListItem($id = "securitySettings", $title);
$menu->addNavigation($menuItem, "", "", "", $navGroup, $navDisplay);
$actionFactory->setModuleAction($menuItem, $innerModules['securitySettings'], "", ".settingsContainer");

// Settings Container
$settingsContainer = HTML::select(".settingsContainer")->item(0);
if (empty($section))
{
	$personalContainer = $page->getModuleContainer($innerModules['generalSettings'], $action = "", $attr = array(), $startup = TRUE, $containerID = "");
	DOM::append($settingsContainer, $personalContainer);
}


return $page->getReport();
//#section_end#
?>