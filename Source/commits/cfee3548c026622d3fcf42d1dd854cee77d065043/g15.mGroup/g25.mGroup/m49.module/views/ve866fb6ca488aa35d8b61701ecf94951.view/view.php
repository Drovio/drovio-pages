<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

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
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Build the page
$pageTitle =  moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "databaseManager");

// Toolbar Navigation
$vcsItem = $page->addToolbarNavItem("sqlVcs", $title = "VCS", $class = "vcs");
$actionFactory->setPopupAction($vcsItem, $moduleID, "vcsCommitManager");

$navCollection = $page->getRibbonCollection("dbNav");
$subItem = $page->addToolbarNavItem("dbNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$newSQL = $panel->build("databaseLibrary", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_querySQL");
$newQueryItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newQueryItem, $moduleID, "createQuery");
$title = moduleLiteral::get($moduleID, "lbl_domain");
$newDomainItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newDomainItem, $moduleID, "createDomain");
DOM::append($navCollection, $newSQL);


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $viewer);

// Query Editor
$WIDE = new redWIDE();
$databaseWIDE = $WIDE->build()->get();
$splitter->appendToMain($databaseWIDE);

// Query Viewer lbl_refresh
$toolbar = DOM::create("div", "", "", "queryControlToolbar");
$splitter->appendToSide($toolbar);
// Refresh Tool
$refreshTool = DOM::create("div", "", "refreshQueries", "toolbarTool");
$refreshTitle = moduleLiteral::get($moduleID, "lbl_refresh");
DOM::append($refreshTool, $refreshTitle);
DOM::append($toolbar, $refreshTool);
$queryViewer = $page->getModuleContainer($moduleID, $action = "queryViewer", $attr = array(), $startup = TRUE, $containerID = "queryViewer");
$splitter->appendToSide($queryViewer);

// Return output
return $page->getReport();
//#section_end#
?>