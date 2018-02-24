<?php
//#section#[header]
// Module Declaration
$moduleID = 196;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\sideMenu;


// Get Variables
$extensionID = $_GET['id'];
$extensionID = 16;

// Create Module Page
$HTMLModulePage = new HTMLModulePage("simpleFullScreen");
$actionFactory = $HTMLModulePage->getActionFactory();
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);

// Build the module
$HTMLModulePage->build($pageTitle);

// Try to load Object


// Object Loaded - Continue
$defAttr = array();
$defAttr['id'] = $wsID;

//____________________ Build Top Navigation ____________________//
// Build ToolBar Menu
// Static Navigation Attributes
$nav_targetcontainer = "pageContent";
$nav_targetgroup = "designerOptionsGroup";
$nav_navgroup = "designerOptionsNavGroup";
$nav_display = "none"; 

// Build Side Navigation Menu
$sideMenu_builder = new sideMenu();

// Configuration Pane menu item
$menuElement = moduleLiteral::get($moduleID, "lbl_configWebsite");
$ref = 'configPane';
$menuItem =  $HTMLModulePage->addToolbarNavItem("1", $menuElement, "");
$sideMenu_builder->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

// Desinger Pane menu item
$menuElement = moduleLiteral::get($moduleID, "lbl_designWebsite");
$ref = 'designerPane';
$menuItem =  $HTMLModulePage->addToolbarNavItem("2", $menuElement, "");
$sideMenu_builder->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
DOM::appendAttr($menuItem, "class", "selected");

//____________________ Build Top Navigation ____________________//__________End


// Build Page Content
$globalContainer = DOM::create("div", "", "");
DOM::attr($globalContainer, "style", "height:100%;");
$HTMLModulePage->appendToSection("mainContent", $globalContainer);

// Configuration Pane
$settingsPane = $HTMLModulePage->getModuleContainer($moduleID, 'configurator', $defAttr, $startup = TRUE, 'configPane');
$sideMenu_builder->addNavigationSelector($settingsPane, $nav_targetgroup);
DOM::append($globalContainer, $settingsPane);

// Desinger Pane
$assetsPane = $HTMLModulePage->getModuleContainer($moduleID, 'designer', $defAttr, $startup = TRUE, 'designerPane');
$sideMenu_builder->addNavigationSelector($assetsPane, $nav_targetgroup);
DOM::append($globalContainer, $assetsPane);

// Return output
return $HTMLModulePage->getReport();
//#section_end#
?>