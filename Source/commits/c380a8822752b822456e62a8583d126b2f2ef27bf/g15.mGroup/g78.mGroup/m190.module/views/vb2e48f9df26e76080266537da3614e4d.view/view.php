<?php
//#section#[header]
// Module Declaration
$moduleID = 190;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkMain'] = 56;
$innerModules['sdkEditor'] = 56;
$innerModules['sqlEditor'] = 49;
$innerModules['ajaxEditor'] = 95;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Core\components\ribbon\rPanel;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();


// Build the module
$page->build("", "coreDeveloper", TRUE);
$uiMainContent = HTML::select(".uiMainContent")->item(0);

// Toolbar Navigation


// SDK Nav
$navCollection = $page->getRibbonCollection("sdkNav");
$subItem = $page->addToolbarNavItem("sdkNavSub", $title = "SDK", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new rPanel();
$newLibPkg = $panel->build("library_package", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sdkLibrary");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newLibItem, $innerModules['sdkEditor'], "createLibrary");
$title = moduleLiteral::get($moduleID, "lbl_sdkPackage");
$newPkgItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newPkgItem, $innerModules['sdkEditor'], "createPackage");
DOM::append($navCollection, $newLibPkg);

$panel = new rPanel();
$newNsObj = $panel->build("ns_object", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sdkNamespace");
$newNamespace = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newNamespace, $innerModules['sdkEditor'], "createNamespace");
$title = moduleLiteral::get($moduleID, "lbl_sdkObject");
$newObject = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newObject, $innerModules['sdkEditor'], "createObject");
DOM::append($navCollection, $newNsObj);


// SQL Nav
$navCollection = $page->getRibbonCollection("dbNav");
$subItem = $page->addToolbarNavItem("dbNavSub", $title = "SQL", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new rPanel();
$newSQLDomain = $panel->build("databaseLibrary")->get();
$title = moduleLiteral::get($moduleID, "lbl_sqlDomain");
$newDomainItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newDomainItem, $innerModules['sqlEditor'], "createDomain");
DOM::append($navCollection, $newSQLDomain);

$panel = new rPanel();
$newSQLQuery = $panel->build("databaseLibrary")->get();
$title = moduleLiteral::get($moduleID, "lbl_sqlQuery");
$newQueryItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newQueryItem, $innerModules['sqlEditor'], "createQuery");
DOM::append($navCollection, $newSQLQuery);


// Ajax Nav
$navCollection = $page->getRibbonCollection("ajxNav");
$subItem = $page->addToolbarNavItem("ajxNavSub", $title = "Ajax", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new rPanel();
$newAjaxDir = $panel->build("newPanel")->get();
$title = moduleLiteral::get($moduleID, "lbl_ajaxDirectory");
$newDirItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newDirItem, $innerModules['ajaxEditor'], "createDirectory");
DOM::append($navCollection, $newAjaxDir);

$panel = new rPanel();
$newAjaxPage = $panel->build("newPanel")->get();
$title = moduleLiteral::get($moduleID, "lbl_ajaxPage");
$newPageItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newPageItem, $innerModules['ajaxEditor'], "createPage");
DOM::append($navCollection, $newAjaxPage);




// Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "Core Explorer")->get();
DOM::append($uiMainContent, $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Library Viewer
$coreViewer = DOM::create("div", "", "", "coreViewer");
$splitter->appendToSide($coreViewer);

// Create tabber
$coreTabber = new tabControl();
$coreSectionsTabber = $coreTabber->build($id = "coreSectionsTabber", TRUE)->get();
DOM::append($coreViewer, $coreSectionsTabber);


// Tabs

// SDK Libraries Tab
$SDKContainer = $page->getModuleContainer($innerModules['sdkEditor'], "packageViewer");
$coreTabber->insertTab("sdkTab", "SDK", $SDKContainer, TRUE);

// SQL Query Tab
$SQLContainer = $page->getModuleContainer($innerModules['sqlEditor'], "queryViewer");
$coreTabber->insertTab("sqlTab", "SQL", $SQLContainer, FALSE);

// Ajax Page Tab
$AjaxContainer = $page->getModuleContainer($innerModules['ajaxEditor'], "ajaxPageViewer");
$coreTabber->insertTab("ajaxTab", "Ajax", $AjaxContainer, FALSE);


// Return output
return $page->getReport("", FALSE);
//#section_end#
?>