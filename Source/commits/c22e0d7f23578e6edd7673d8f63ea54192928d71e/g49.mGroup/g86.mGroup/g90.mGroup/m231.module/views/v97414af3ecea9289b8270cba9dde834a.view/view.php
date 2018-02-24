<?php
//#section#[header]
// Module Declaration
$moduleID = 231;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkManager'] = 247;
$innerModules['webCoreSettings'] = 298;

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
importer::import("DEV", "WebEngine");
importer::import("UI", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Core\components\ribbon\rPanel;
use \UI\Presentation\gridSplitter;
use \UI\Developer\devTabber;
use \DEV\WebEngine\webCoreProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "webCoreDeveloper");

// Set toolbar action attributes
$attr = array();
$attr['id'] = webCoreProject::PROJECT_ID;

// Build Top Navigation
$title = moduleLiteral::get($moduleID, "lbl_webCoreSettings");
$subItem = $page->addToolbarNavItem("webCoreSettings", $title, $class = "settings", $navCollection = NULL, $ribbonType = "float", $type = "obedient toggle", TRUE);
$actionFactory->setModuleAction($subItem, $innerModules['webCoreSettings'], "", "", $attr);


$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
$page->append($viewer);


// Insert WIDE mechanics
$WIDE = new devTabber();
$ebuilderAPI_WIDE = $WIDE->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($ebuilderAPI_WIDE);

// Sidebar Query Viewer/Manager
$packageViewerContainer = DOM::create("div", "", "", "webCoreExplorer");
$splitter->appendToSide($packageViewerContainer);

// Acquire browsing info
$packageViewer = $page->getModuleContainer($innerModules['sdkManager'], "packageViewer", $attr);
DOM::append($packageViewerContainer, $packageViewer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>