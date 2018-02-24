<?php
//#section#[header]
// Module Declaration
$moduleID = 233;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkManager'] = 234;
$innerModules['ajaxManager'] = 236;
$innerModules['sqlManager'] = 238;

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
importer::import("DEV", "Core");
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
use \UI\Presentation\tabControl;
use \UI\Developer\devTabber;
use \DEV\Core\coreProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "coreDeveloper");

// Set global action attributes
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;


// Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE, "Core Explorer")->get();
$page->append($viewer);

// redWIDE
$wide = new devTabber();
$ajaxWide = $wide->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Library Viewer
$coreViewer = DOM::create("div", "", "", "coreViewer");
$splitter->appendToSide($coreViewer);

// Create tabber
$coreTabber = new tabControl();
$coreSectionsTabber = $coreTabber->build($id = "coreSectionsTabber", TRUE, FALSE)->get();
DOM::append($coreViewer, $coreSectionsTabber);


// Tabs

// SDK Libraries Tab
$SDKContainer = $page->getModuleContainer($innerModules['sdkManager'], "packageViewer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$coreTabber->insertTab("sdkTab", "SDK", $SDKContainer, TRUE);

// SQL Query Tab
$SQLContainer = $page->getModuleContainer($innerModules['sqlManager'], "queryExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$coreTabber->insertTab("sqlTab", "SQL", $SQLContainer, FALSE);

// Ajax Page Tab
$AjaxContainer = $page->getModuleContainer($innerModules['ajaxManager'], "ajaxExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$coreTabber->insertTab("ajaxTab", "Ajax", $AjaxContainer, FALSE);


// Return output
return $page->getReport("", FALSE);
//#section_end#
?>