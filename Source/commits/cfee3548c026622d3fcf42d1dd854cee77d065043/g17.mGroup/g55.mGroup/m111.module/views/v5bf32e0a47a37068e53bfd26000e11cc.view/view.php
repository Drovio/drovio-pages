<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

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
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page->build($pageTitle, "layoutManager");


// Split Screen
$splitter = new gridSplitter();
$outerHolder = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $outerHolder);

// _____ TOP NAVIGATION _____//
$navCollection = $page->getRibbonCollection("layoutNav");
$subItem = $page->addToolbarNavItem("layoutNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Create new moduleGroup
$panel = new ribbonPanel();
$newLayoutPanel = $panel->build("layout", TRUE)->get();
$title = moduleLiteral::get($moduleID, "createLayout");
$newLayout = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newLayout, $moduleID, "newLayout");
DOM::append($navCollection, $newLayoutPanel);
// _____ TOP NAVIGATION _____//_____END


$WIDE = new redWIDE();
$layout_WIDE = $WIDE->build();
$splitter->appendToMain($layout_WIDE->get());

// Sidebar LayoutLIst Viewer
$moduleWrapper = DOM::create("div", "", "layoutViewer");
$splitter->appendToSide($moduleWrapper);

$layoutListViewer = HTMLModulePage::getModuleContainer($moduleID, "layoutListViewer", $attr = array(), $startup = TRUE, $containerID = "");
DOM::append($moduleWrapper, $layoutListViewer);


// Return output
return $page->getReport();
//#section_end#
?>