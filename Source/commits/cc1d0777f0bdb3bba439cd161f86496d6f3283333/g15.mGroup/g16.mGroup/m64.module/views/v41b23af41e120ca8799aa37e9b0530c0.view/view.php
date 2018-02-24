<?php
//#section#[header]
// Module Declaration
$moduleID = 64;

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

$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);

// Create page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();
// Build the module
$page->build("", "devModulePage");

$navCollection = $page->getRibbonCollection("devHome");
$subItem = $page->addToolbarNavItem("devNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Create new moduleGroup
$panel = new ribbonPanel();
$newGrp = $panel->build("moduleGroup", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_newModuleGroup");
$newGroup = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newGroup, $moduleID, "newModuleGroup");
$title = moduleLiteral::get($moduleID, "lbl_newModule");
$newModuleItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newModuleItem, $moduleID, "newModule");
DOM::append($navCollection, $newGrp);

// Create new module / module view
$panel = new ribbonPanel();
$newModComp = $panel->build("moduleComponents", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_newModuleView");
$newViewItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newViewItem, $moduleID, "newModuleView");
$title = moduleLiteral::get($moduleID, "lbl_newModuleQuery");
$newQueryItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
//$actionFactory->setModuleAction($newQueryItem, $moduleID, "newModuleQuery");
DOM::append($navCollection, $newModComp);


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "Modules")->get();
$page->appendToSection("mainContent", $viewer);


// WIDE
$wide = new redWIDE();
$moduleWide = $wide->build()->get();
$splitter->appendToMain($moduleWide);


// Sidebar Module Explorer
$devManagementArea = DOM::create("div", "", "", "devManagementArea");
$splitter->appendToSide($devManagementArea);
$viewerContainer = HTMLModulePage::getModuleContainer($moduleID, "moduleExplorer");
DOM::append($devManagementArea, $viewerContainer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>