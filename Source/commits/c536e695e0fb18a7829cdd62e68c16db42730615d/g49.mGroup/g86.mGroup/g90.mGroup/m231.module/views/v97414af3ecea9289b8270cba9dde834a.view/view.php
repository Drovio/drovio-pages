<?php
//#section#[header]
// Module Declaration
$moduleID = 231;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkManager'] = 247;
$innerModules['webCoreSettings'] = 298;

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
importer::import("DEV", "WebEngine");
importer::import("UI", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Core\components\ribbon\rPanel;
use \UI\Presentation\gridSplitter;
use \UI\Developer\devTabber;
use \DEV\WebEngine\webCoreProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "webCoreDeveloper");

// Set toolbar action attributes
$attr = array();
$attr['id'] = webCoreProject::PROJECT_ID;

// Build Top Navigation
$title = moduleLiteral::get($moduleID, "lbl_webCoreSettings");
$subItem = $page->addToolbarNavItem("webCoreSettings", $title, $class = "settings", $navCollection = NULL, $ribbonType = "float", $type = "obedient toggle", TRUE);
$actionFactory->setModuleAction($subItem, $innerModules['webCoreSettings'], "", "", $attr);

$navCollection = $page->getRibbonCollection("webCoreSDK");
$title = moduleLiteral::get($moduleID, "lbl_webCoreSDK");
$subItem = $page->addToolbarNavItem("webCoreSDK", $title, $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", TRUE);


// Toolbar Navigation
$panel = new rPanel();
$libTitle = moduleLiteral::get($moduleID, "lbl_library");
$new_libItem = $panel->build("", TRUE)->insertPanelItem($type = "small", $title = $libTitle);
$actionFactory->setModuleAction($new_libItem, $innerModules['sdkManager'], "createLibrary", "", $attr);
$pkgTitle = moduleLiteral::get($moduleID, "lbl_package");
$new_pkgItem = $panel->insertPanelItem($type = "small", $title = $pkgTitle);
$actionFactory->setModuleAction($new_pkgItem, $innerModules['sdkManager'], "createPackage", "", $attr);
DOM::append($navCollection, $panel->get());

// Create Namespace/Object
$panel = new rPanel();
$nsTitle = moduleLiteral::get($moduleID, "lbl_namespace");
$new_nsItem = $panel->build("", TRUE)->insertPanelItem($type = "small", $title = $nsTitle);
$actionFactory->setModuleAction($new_nsItem, $innerModules['sdkManager'], "createNamespace", "", $attr);
$objTitle = moduleLiteral::get($moduleID, "lbl_object");
$new_objItem = $panel->insertPanelItem($type = "small", $title = $objTitle);
$actionFactory->setModuleAction($new_objItem, $innerModules['sdkManager'], "createObject", "", $attr);
DOM::append($navCollection, $panel->get());


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->append($viewer);


// Insert WIDE mechanics
$WIDE = new devTabber();
$ebuilderAPI_WIDE = $WIDE->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($ebuilderAPI_WIDE);

// Sidebar Query Viewer/Manager
$packageViewerContainer = DOM::create("div", "", "", "webCoreExplorer");
$splitter->appendToSide($packageViewerContainer);

// Acquire browsing info
$packageViewer = $page->getModuleContainer($innerModules['sdkManager'], "packageViewer", $attr);
DOM::append($packageViewerContainer, $packageViewer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>