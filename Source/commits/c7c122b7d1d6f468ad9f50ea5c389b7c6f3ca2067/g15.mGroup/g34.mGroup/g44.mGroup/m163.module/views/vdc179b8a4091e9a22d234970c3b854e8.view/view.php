<?php
//#section#[header]
// Module Declaration
$moduleID = 163;

// Inner Module Codes
$innerModules = array();
$innerModules['devPrivileges'] = 85;
$innerModules['userPrivileges'] = 86;
$innerModules['userGroups'] = 90;

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


$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page = new HTMLModulePage("freeLayout");
$page->build($pageTitle);
$actionFactory = $page->getActionFactory();
 
$devMenu = new sideMenu();

// Static Navigation Attributes
$nav_ref = "toolBarNavigationMenu";
$nav_targetcontainer = "toolBarNavigationMenu";
$nav_targetgroup = "toolBarNavigationMenu";
$nav_navgroup = "toolBarNavigationMenu";


// Set Menu Content
$title = moduleLiteral::get($moduleID, "lbl_devTlbTitle", FALSE);
$tlb_devPrivs_item = $page->addToolbarNavItem("devPrivs", $title, "dev_privileges");
$devMenu->addNavigation($tlb_devPrivs_item , $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
//DOM::data($tlb_devPrivs_item, "static-nav", array("navgroup" => "topMenu"));
DOM::appendAttr($tlb_devPrivs_item, "class", 'selected');
$actionFactory->setModuleAction($tlb_devPrivs_item, $innerModules['devPrivileges'], 'moduleProgrammers', '.uiMainContent');

$title = moduleLiteral::get($moduleID, "lbl_userTlbTitle", FALSE);
$tlb_userPrivs_item = $page->addToolbarNavItem("userPrivs", $title, "user_privileges");
$devMenu->addNavigation($tlb_userPrivs_item , $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$actionFactory->setModuleAction($tlb_userPrivs_item, $innerModules['userPrivileges'], '', '.uiMainContent');

$title = moduleLiteral::get($moduleID, "lbl_groupsTlbTitle", FALSE);
$tlb_userPrivs_item = $page->addToolbarNavItem("userGroups", $title, "user_groups");
$devMenu->addNavigation($tlb_userPrivs_item , $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$actionFactory->setModuleAction($tlb_userPrivs_item, $innerModules['userGroups'], '', '.uiMainContent');


// Load Default Page
$content = $page->getModuleContainer($innerModules['devPrivileges'], "moduleProgrammers", $attr = array(), TRUE, $containerID = "");
$page->appendToSection("main", $content);

// Return the report
return $page->getReport(HTMLModulePage::getPageHolder());
//#section_end#
?>