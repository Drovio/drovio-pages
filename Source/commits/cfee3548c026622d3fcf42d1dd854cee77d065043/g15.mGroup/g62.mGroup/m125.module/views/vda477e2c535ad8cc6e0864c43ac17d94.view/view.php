<?php
//#section#[header]
// Module Declaration
$moduleID = 125;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("App Center SDK", "devAppCenter");


// Build Top Navigation
$vcsItem = $page->addToolbarNavItem("appcVCS", $title = "VCS", $class = "vcs");
$actionFactory->setPopupAction($vcsItem, $moduleID, "commitManager");

$navCollection = $page->getRibbonCollection("appCenterNav");
$subItem = $page->addToolbarNavItem("devAppNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 1);

// Ribbon Panels
$panel = new ribbonPanel();
$newLibPkgPanel = $panel->build("newLibPkg")->get();
DOM::append($navCollection, $newLibPkgPanel);

$newPackageItem = $panel->insertPanelItem($type = "small", $title = "New Package", $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newPackageItem, $moduleID, "createPackage");

$panel = new ribbonPanel();
$newNsObjPanel = $panel->build("newNsObj", TRUE)->get();
DOM::append($navCollection, $newNsObjPanel);

$newNamespaceItem = $panel->insertPanelItem($type = "small", $title = "New Namespace", $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newNamespaceItem, $moduleID, "createNamespace");

$newObjectItem = $panel->insertPanelItem($type = "small", $title = "New Object", $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newObjectItem, $moduleID, "createObject");

$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $viewer);

// redWIDE
$wide = new redWIDE();
$appCenterWIDE = $wide->build()->get();
$splitter->appendToMain($appCenterWIDE);


// Sidebar SDK Object Viewer
$appEditor = DOM::create("div", "", "", "appObjectViewer");
$splitter->appendToSide($appEditor);

$viewerContainer = $page->getModuleContainer($moduleID, "packageViewer");
DOM::append($appEditor, $viewerContainer);

// Return output
return $page->getReport();
//#section_end#
?>