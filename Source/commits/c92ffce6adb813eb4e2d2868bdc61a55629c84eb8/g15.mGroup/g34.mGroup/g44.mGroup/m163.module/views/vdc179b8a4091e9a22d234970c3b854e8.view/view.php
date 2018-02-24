<?php
//#section#[header]
// Module Declaration
$moduleID = 163;

// Inner Module Codes
$innerModules = array();
$innerModules['userPrivileges'] = 86;
$innerModules['userGroups'] = 90;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \UI\Navigation\sideMenu;


$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build module page
$title = moduleLiteral::get($moduleID, "lbl_pageTitle", array(), FALSE);
$page->build($title, "priviledgesEditor", TRUE);
 
$devMenu = new sideMenu();

// Static Navigation Attributes
$nav_ref = "toolBarNavigationMenu";
$nav_targetcontainer = "toolBarNavigationMenu";
$nav_targetgroup = "toolBarNavigationMenu";
$nav_navgroup = "toolBarNavigationMenu";


// Set Menu Content
$navCollection = NULL;//$page->getRibbonCollection("priviledgesNav");

$title = moduleLiteral::get($moduleID, "lbl_userTlbTitle");
$subItem = $page->addToolbarNavItem("userPrivs", $title, $class = "", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
$devMenu->addNavigation($subItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$actionFactory->setModuleAction($subItem, $innerModules['userPrivileges'], '', '.priviledgesEditor .contentHolder');
DOM::appendAttr($subItem, 'class', 'selected');

$title = moduleLiteral::get($moduleID, "lbl_groupsTlbTitle");
$subItem = $page->addToolbarNavItem("userGroups", $title, $class = "", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
$devMenu->addNavigation($subItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$actionFactory->setModuleAction($subItem, $innerModules['userGroups'], '', '.priviledgesEditor .contentHolder');


// Load Default Page
$container = HTML::select(".priviledgesEditor .contentHolder")->item(0);
$content = $page->getModuleContainer($innerModules['userPrivileges'], "", $attr = array(), TRUE, $containerID = "");
DOM::append($container, $content);

// Return the report
return $page->getReport();
//#section_end#
?>