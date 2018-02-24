<?php
//#section#[header]
// Module Declaration
$moduleID = 56;

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
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Build the module
$page->build(moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE), "sdkLibraryManager");


// _____ Toolbar Navigation
$vcsItem = $page->addToolbarNavItem("sdkVcs", $title = "VCS", $class = "vcs");
$actionFactory->setPopupAction($vcsItem, $moduleID, "vcsControl");


$navCollection = $page->getRibbonCollection("sdkNav");
$subItem = $page->addToolbarNavItem("sdkNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$newLibPkg = $panel->build("library_package", TRUE)->get();
$newLibItem = $panel->insertPanelItem($type = "small", $title = "New Library", $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newLibItem, $moduleID, "createLibrary");
$newPkgItem = $panel->insertPanelItem($type = "small", $title = "New Package", $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newPkgItem, $moduleID, "createPackage");
DOM::append($navCollection, $newLibPkg);

$panel = new ribbonPanel();
$newNsObj = $panel->build("ns_object", TRUE)->get();
$newNamespace = $panel->insertPanelItem($type = "small", $title = "New Namespace", $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newNamespace, $moduleID, "createNamespace");
$newObject = $panel->insertPanelItem($type = "small", $title = "New Object", $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newObject, $moduleID, "createObject");
DOM::append($navCollection, $newNsObj);

$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "SDK Libraries")->get();
$page->appendToSection("mainContent", $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);


// Sidebar SDK Object Viewer
$sdkEditor = DOM::create("div", "", "", "sdkObjectViewer");
$splitter->appendToSide($sdkEditor);

$viewerContainer = HTMLModulePage::getModuleContainer($moduleID, "packageViewer");
DOM::append($sdkEditor, $viewerContainer);

// Legend
$legend = DOM::create("div", "", "", "sdkLegend");
DOM::append($sdkEditor, $legend);
 
$healthy = DOM::create("span", "Healthy", "", "legendEntry");
DOM::append($legend, $healthy);
$updated = DOM::create("span", "Recently Updated", "", "legendEntry updatedObject");
DOM::append($legend, $updated);
$depr = DOM::create("span", "Deprecated", "", "legendEntry deprecatedObject");
DOM::append($legend, $depr);
$undoc = DOM::create("span", "Undocumented", "", "legendEntry undocumentedObject");
DOM::append($legend, $undoc);

// Return output
return $page->getReport();
//#section_end#
?>