<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

// Inner Module Codes
$innerModules = array();
$innerModules['srcManager'] = 263;
$innerModules['viewManager'] = 266;
$innerModules['libManager'] = 265;
$innerModules['appSettings'] = 172;
$innerModules['projectHints'] = on;

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
importer::import("INU", "Developer");
importer::import("UI", "Core");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Core\components\ribbon\rPanel;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "applicationDesigner", TRUE);

// Action Attributes
$attr = array(); 
$attr['appID'] = $_GET['projectID'];

// Toolbar Navigation
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_settings");
$subItem = $page->addToolbarNavItem("settingsNavSub", $title, $class = "settings", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
$actionFactory->setModuleAction($subItem, $innerModules['appSettings'], "", "", $attr);

// Views Nav
$navCollection = $page->getRibbonCollection("vNav");
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_views");
$subItem = $page->addToolbarNavItem("vNavSub", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

$panel = new rPanel();
$panelObject = $panel->build("delview")->get();
$title = moduleLiteral::get($moduleID, "lbl_removeViewFolder");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['viewManager'], "deleteViewFolder", "", $attr);
DOM::append($navCollection, $panelObject);

$panel = new rPanel();
$panelObject = $panel->build("newview")->get();
$title = moduleLiteral::get($moduleID, "lbl_newViewFolder");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['viewManager'], "createViewFolder", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_newView");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['viewManager'], "createView", "", $attr);
DOM::append($navCollection, $panelObject);

// Library Nav
$navCollection = $page->getRibbonCollection("libNav");
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_lib");
$subItem = $page->addToolbarNavItem("libNavSub", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

$panel = new rPanel();
$panelObject = $panel->build("cssstyle", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_libStyle");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['libManager'], "createStyle", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_libScript");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['libManager'], "createScript", "", $attr);
DOM::append($navCollection, $panelObject);

// Source Nav
$navCollection = $page->getRibbonCollection("srcNav");
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_source");
$subItem = $page->addToolbarNavItem("sourceNavSub", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

$panel = new rPanel();
$panelObject = $panel->build("package", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sourcePackage");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['srcManager'], "createPackage", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_sourceNamespace");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['srcManager'], "createNamespace", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_sourceObject");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($panelItem, $innerModules['srcManager'], "createObject", "", $attr);
DOM::append($navCollection, $panelObject);





$mainContent = HTML::select(".uiMainContent")->item(0);

// Build Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Explorer")->get();
DOM::append($mainContent, $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Library Viewer
$applicationExplorer = DOM::create("div", "", "", "applicationExplorer");
$splitter->appendToSide($applicationExplorer);

// Create tabber
$appTabber = new tabControl();
$appSectionsTabber = $appTabber->build($id = "appSectionsTabber", TRUE)->get();
DOM::append($applicationExplorer, $appSectionsTabber);


// Tabs
$attr = array();
$attr['appID'] = $_GET['projectID'];

// Application Views Tab
$title = moduleLiteral::get($moduleID, "lbl_viewsTab");
$mContainer = $page->getModuleContainer($innerModules['viewManager'], "viewExplorer", $attr);
$appTabber->insertTab("vTab", $title, $mContainer, TRUE);

// Application Library Tab (Scripts and Styles)
$title = moduleLiteral::get($moduleID, "lbl_libTab");
$mContainer = $page->getModuleContainer($innerModules['libManager'], "libExplorer", $attr);
$appTabber->insertTab("libTab", $title, $mContainer, FALSE);

// Application Source Tab
$title = moduleLiteral::get($moduleID, "lbl_sourceTab");
$mContainer = $page->getModuleContainer($innerModules['srcManager'], "packageExplorer", $attr);
$appTabber->insertTab("srcTab", $title, $mContainer, FALSE);


// Return output
return $page->getReport("", FALSE);
//#section_end#
?>