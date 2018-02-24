<?php
//#section#[header]
// Module Declaration
$moduleID = 300;

// Inner Module Codes
$innerModules = array();

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \ESS\Environment\url;
use \UI\Html\MPage;
//use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "extensionDesigner", TRUE);

// _____ Toolbar Navigation

// Action Attributes
$attr = array();
$attr['appID'] = $_GET['projectID'];

// Add Source Objects
$navCollection = $page->getRibbonCollection("sourceNav");
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_source");
$subItem = $page->addToolbarNavItem("sourceNavSub", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$rPanel = $panel->build("package_namespace", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sourceLibrary");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createSourceLibrary", $attr);
$title = moduleLiteral::get($moduleID, "lbl_sourcePackage");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createSourcePackage", $attr);
DOM::append($navCollection, $rPanel);

$panel = new ribbonPanel();
$rPanel = $panel->build("ns_object", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sourceNamespace");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createSourceNamespace", $attr);
$title = moduleLiteral::get($moduleID, "lbl_sourceObject");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createSourceObject", $attr);
DOM::append($navCollection, $rPanel);

// Add Views Object
$navCollection = $page->getRibbonCollection("viewsNav");
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_views");
$subItem = $page->addToolbarNavItem("viewsNavSub", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);


$panel = new ribbonPanel();
$rPanel = $panel->build("ns_object", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_viewObject");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createExtView", $attr);
DOM::append($navCollection, $rPanel);

$rPanel = $panel->build("views_resources", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_styleObject");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createExtTheme", $attr);
$title = moduleLiteral::get($moduleID, "lbl_scriptObject");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($panelItem, $moduleID, "createExtScript", $attr);
DOM::append($navCollection, $rPanel);

/*
// Application Settings
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_settings");
$subItem = $page->addToolbarNavItem("settingsSub", $title, $class = "devext settings", NULL);
$actionFactory->setPopupAction($subItem, $moduleID, "appSettings", $attr);
/*
// Application Literals
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_literals");
$subItem = $page->addToolbarNavItem("literalSub", $title, $class = "devapp literals", NULL);
$actionFactory->setPopupAction($subItem, $innerModules['literalManager'], "", $attr);
*/

$mainContent = HTML::select(".uiMainContent")->item(0);

// Build Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Explorer")->get();
DOM::append($mainContent, $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Application Section Viewer
$applicationExplorerSide = DOM::create("div", "", "extensionExplorerSide");
$splitter->appendToSide($applicationExplorerSide);

$attr = array();
$attr['appID'] = $_GET['projectID'];
$viewerContainer = $page->getModuleContainer($moduleID, "extExplorer", $attr);
DOM::append($applicationExplorerSide, $viewerContainer);


// Return output
return $page->getReport("", FALSE);
//#section_end#
?>