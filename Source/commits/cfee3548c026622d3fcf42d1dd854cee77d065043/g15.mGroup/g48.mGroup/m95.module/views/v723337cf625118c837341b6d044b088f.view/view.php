<?php
//#section#[header]
// Module Declaration
$moduleID = 95;

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
$page->build(moduleLiteral::get($moduleID, "title", FALSE), "ajaxPageManager");


// _____ Toolbar Navigation
$vcsItem = $page->addToolbarNavItem("ajaxVcs", $title = "VCS", $class = "vcs");
$actionFactory->setPopupAction($vcsItem, $moduleID, "commitManager");


$navCollection = $page->getRibbonCollection("ajxNav");
$subItem = $page->addToolbarNavItem("ajxNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$newDir = $panel->build("newPanel")->get();
$newDirItem = $panel->insertPanelItem($type = "small", $title = "New Directory", $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newDirItem, $moduleID, "createDirectory");
DOM::append($navCollection, $newDir);

$panel = new ribbonPanel();
$newPage = $panel->build("newPanel")->get();
$newPageItem = $panel->insertPanelItem($type = "small", $title = "New Page", $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newPageItem, $moduleID, "createPage");
DOM::append($navCollection, $newPage);

$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);

// ajaxPageNavigator
$viewerContainer = $page->getModuleContainer($moduleID, "ajaxPageViewer");
$splitter->appendToSide($viewerContainer);

// Return output
return $page->getReport();
//#section_end#
?>