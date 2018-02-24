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
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\sideMenu;

$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "mySettingsPage");

// Get Settings section
$section = $_GET['section'];


// Create sidemenu
$menu = new sideMenu();
$sideMenu = $menu->build("settingsMenu")->get();
$page->appendToSection("sidebar", $sideMenu);

// Navigation Attributes
$navGroup = "settingsNav";
$navDisplay = "none";

// General Settings
$title = moduleLiteral::get($moduleID, "lbl_generalSettings");
$menuItem = $menu->insertListItem($id = "generalSettings", $title);
$menu->addNavigation($menuItem, "", "", "", $navGroup, $navDisplay);
if (empty($section))
	DOM::appendAttr($menuItem, "class", "selected");
$actionFactory->setModuleAction($menuItem, $innerModules['generalSettings'], "", "#settingsContainer");

// Security Settings
$title = moduleLiteral::get($moduleID, "lbl_securitySettings");
$menuItem = $menu->insertListItem($id = "securitySettings", $title);
$menu->addNavigation($menuItem, "", "", "", $navGroup, $navDisplay);
$actionFactory->setModuleAction($menuItem, $innerModules['securitySettings'], "", "#settingsContainer");




// Settings Container
$settingsContainer = DOM::create("div", "", "settingsContainer");
$page->appendToSection("mainContent", $settingsContainer);

if (empty($section))
{
	$personalContainer = $page->getModuleContainer($innerModules['generalSettings'], $action = "", $attr = array(), $startup = TRUE, $containerID = "");
	DOM::append($settingsContainer, $personalContainer);
}


return $page->getReport();
//#section_end#
?>