<?php
//#section#[header]
// Module Declaration
$moduleID = 34;

// Inner Module Codes
$innerModules = array();
$innerModules['createModule'] = 36;
$innerModules['createGroup'] = 37;

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
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);

// Create page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();
// Build the module
$page->build($pageTitle, "managerModule");

// _____ Toolbar Navigation
$navCollection = $page->getRibbonCollection("devHome");
$subItem = $page->addToolbarNavItem("devNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Create new moduleGroup
$panel = new ribbonPanel();
$newGrp = $panel->build("moduleGroup", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_addGroup");
$newGroup = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newGroup, $innerModules['createGroup']);
DOM::append($navCollection, $newGrp);

// Create new module / auxiliary
$panel = new ribbonPanel();
$newModAux = $panel->build("module_aux", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_addModule");
$newModuleItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$attr = array();
$attr['type'] = "module";
$actionFactory->setModuleAction($newModuleItem, $innerModules['createModule'], "", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_addAuxModule");
$newAuxItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$attr['type'] = "aux";
$actionFactory->setModuleAction($newAuxItem, $innerModules['createModule'], "", "", $attr);
DOM::append($navCollection, $newModAux);


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "Modules")->get();
$page->appendToSection("mainContent", $viewer);


// redWIDE
$wide = new redWIDE();
$moduleWide = $wide->build()->get();
$splitter->appendToMain($moduleWide);


// Sidebar SDK Object Viewer
$devManagementArea = DOM::create("div", "", "", "devManagementArea");
$splitter->appendToSide($devManagementArea);
$viewerContainer = HTMLModulePage::getModuleContainer($moduleID, "moduleViewer");
DOM::append($devManagementArea, $viewerContainer);

// Return output
return $page->getReport();
//#section_end#
?>