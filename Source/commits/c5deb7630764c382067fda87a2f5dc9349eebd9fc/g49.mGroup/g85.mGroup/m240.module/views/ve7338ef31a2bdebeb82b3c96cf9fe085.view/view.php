<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

// Inner Module Codes
$innerModules = array();

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
importer::import("DEV", "Modules");
importer::import("UI", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Core\components\ribbon\rPanel;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \UI\Developer\devTabber;
use \DEV\Modules\modulesProject;

$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);

// Create page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();
// Build the module
$page->build("", "devModulePage");

$navCollection = $page->getRibbonCollection("devHome");
$subItem = $page->addToolbarNavItem("devNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

// Set toolbar action attributes
$attr = array();
$attr['id'] = modulesProject::PROJECT_ID;

// Create new moduleGroup
$panel = new rPanel();
$newGrp = $panel->build("moduleGroup", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_newModuleGroup");
$newGroup = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newGroup, $moduleID, "newModuleGroup", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_newModule");
$newModuleItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newModuleItem, $moduleID, "newModule", "", $attr);
DOM::append($navCollection, $newGrp);

// Create new module / module view
$panel = new rPanel();
$newModComp = $panel->build("moduleComponents", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_newModuleView");
$newViewItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newViewItem, $moduleID, "newModuleView", "", $attr);
$title = moduleLiteral::get($moduleID, "lbl_newModuleQuery");
$newQueryItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newQueryItem, $moduleID, "newModuleQuery", "", $attr);
DOM::append($navCollection, $newModComp);

$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "Modules")->get();
$page->append($viewer);


// WIDE
$wide = new devTabber();
$moduleWide = $wide->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($moduleWide);


// Sidebar Module Explorer
$devManagementArea = DOM::create("div", "", "", "devManagementArea");
$splitter->appendToSide($devManagementArea);
$viewerContainer = $page->getModuleContainer($moduleID, "moduleExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
DOM::append($devManagementArea, $viewerContainer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>