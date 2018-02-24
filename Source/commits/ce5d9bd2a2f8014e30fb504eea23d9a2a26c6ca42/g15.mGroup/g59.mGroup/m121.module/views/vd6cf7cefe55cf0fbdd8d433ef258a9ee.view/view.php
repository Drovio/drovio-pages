<?php
//#section#[header]
// Module Declaration
$moduleID = 121;

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
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "devEbuilderAPI");


// Build Top Navigation
$navCollection = $page->getRibbonCollection("ebldApiNav");
$subItem = $page->addToolbarNavItem("ebldApiNav", $title = "", $class = "add_new", $collection = $navCollection, $ribbonType = "float", $type = "obedient", $pinnable = FALSE, $index = 0);
// _____ Toolbar Navigation Menu
// Create Library/Package
$new_libPanel = new ribbonPanel();
//___ Library
$libTitle = moduleLiteral::get($moduleID, "lbl_library");
$new_libItem = $new_libPanel->build("", TRUE)->insertPanelItem($type = "small", $title = $libTitle);
$actionFactory->setModuleAction($new_libItem, $moduleID, "createLibrary");
//___ Package
$pkgTitle = moduleLiteral::get($moduleID, "lbl_package");
$new_pkgItem = $new_libPanel->insertPanelItem($type = "small", $title = $pkgTitle);
$actionFactory->setModuleAction($new_pkgItem, $moduleID, "createPackage");
HTMLRibbon::insertItem($navCollection, $new_libPanel->get());

// Create Namespace/Object
$new_objPanel = new ribbonPanel();
//___ Namespace
$nsTitle = moduleLiteral::get($moduleID, "lbl_namespace");
$new_nsItem = $new_objPanel->build("", TRUE)->insertPanelItem($type = "small", $title = $nsTitle);
$actionFactory->setModuleAction($new_nsItem, $moduleID, "createNamespace");
//___ Object
$objTitle = moduleLiteral::get($moduleID, "lbl_item");
$new_objItem = $new_objPanel->insertPanelItem($type = "small", $title = $objTitle);
$actionFactory->setModuleAction($new_objItem, $moduleID, "createObject");
HTMLRibbon::insertItem($navCollection, $new_objPanel->get());

//____________________ Build Top Navigation ____________________//__________End


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $viewer);


// Insert WIDE mechanics
$WIDE = new redWIDE();
$ebuilderAPI_WIDE = $WIDE->build()->get();
$splitter->appendToMain($ebuilderAPI_WIDE);

// Sidebar Query Viewer/Manager
$apiElementsViewer = DOM::create("div", "", "packageViewerContainer");
$splitter->appendToSide($apiElementsViewer);

// Acquire browsing info
$ebuilderAPIViewer = HTMLModulePage::getModuleContainer($moduleID, "packageViewer");
DOM::append($apiElementsViewer, $ebuilderAPIViewer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>