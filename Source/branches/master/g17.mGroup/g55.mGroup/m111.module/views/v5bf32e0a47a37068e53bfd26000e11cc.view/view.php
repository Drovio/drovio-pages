<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Core\components\ribbon\rPanel;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new MPage();
$actionFactory = $page->getActionFactory();

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "layoutManagerPage", TRUE);
$uiMainContent = HTML::select(".uiMainContent")->item(0);

// Split Screen
$splitter = new gridSplitter();
$outerHolder = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
DOM::append($uiMainContent, $outerHolder);


$navCollection = $page->getRibbonCollection("layoutNav");
$subItem = $page->addToolbarNavItem("layoutNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Create new moduleGroup
$panel = new rPanel();
$newLayoutPanel = $panel->build("layout", TRUE)->get();
$title = moduleLiteral::get($moduleID, "createLayout");
$newLayout = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newLayout, $moduleID, "newLayout");
DOM::append($navCollection, $newLayoutPanel);


$WIDE = new redWIDE();
$layout_WIDE = $WIDE->build();
$splitter->appendToMain($layout_WIDE->get());

// Sidebar LayoutLIst Viewer
$moduleWrapper = DOM::create("div", "", "layoutViewer");
$splitter->appendToSide($moduleWrapper);

$layoutListViewer = $page->getModuleContainer($moduleID, "layoutListViewer", $attr = array(), $startup = TRUE, $containerID = "");
DOM::append($moduleWrapper, $layoutListViewer);


// Return output
return $page->getReport();
//#section_end#
?>