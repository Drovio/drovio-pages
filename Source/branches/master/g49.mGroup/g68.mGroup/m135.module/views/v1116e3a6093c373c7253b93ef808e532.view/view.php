<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

// Inner Module Codes
$innerModules = array();
$innerModules['srcManager'] = 263;
$innerModules['viewManager'] = 266;
$innerModules['libManager'] = 265;
$innerModules['appSettings'] = 172;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("UI", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Core\components\ribbon\rPanel;
use \UI\Developer\devTabber;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build the module
$page->build("", "applicationDesigner");

// Action Attributes
$attr = array(); 
$attr['appID'] = $projectID;
$attr['id'] = $projectID;

// Toolbar Navigation
$title = moduleLiteral::get($moduleID, "lbl_toolbarNav_settings");
$subItem = $page->addToolbarNavItem("settingsNavSub", $title, $class = "app_settings", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
$actionFactory->setModuleAction($subItem, $innerModules['appSettings'], "", "", $attr);


// Build Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Explorer")->get();
$page->append($viewer);

// Create the developer's dynamic tabber for application components
$wide = new devTabber();
$ajaxWide = $wide->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Library Viewer
$applicationExplorer = DOM::create("div", "", "", "applicationExplorer");
$splitter->appendToSide($applicationExplorer);

// Create tabber
$appTabber = new tabControl();
$appSectionsTabber = $appTabber->build($id = "appSectionsTabber", TRUE, FALSE)->get();
DOM::append($applicationExplorer, $appSectionsTabber);


// Tabs
$attr = array();
$attr['appID'] = $projectID;
$attr['id'] = $projectID;

// Application Views Tab
$title = moduleLiteral::get($moduleID, "lbl_viewsTab");
$mContainer = $page->getModuleContainer($innerModules['viewManager'], "viewExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$appTabber->insertTab("vTab", $title, $mContainer, TRUE);

// Application Library Tab (Scripts and Styles)
$title = moduleLiteral::get($moduleID, "lbl_libTab");
$mContainer = $page->getModuleContainer($innerModules['libManager'], "libExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$appTabber->insertTab("libTab", $title, $mContainer, FALSE);

// Application Source Tab
$title = moduleLiteral::get($moduleID, "lbl_sourceTab");
$mContainer = $page->getModuleContainer($innerModules['srcManager'], "packageExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
$appTabber->insertTab("srcTab", $title, $mContainer, FALSE);


// Return output
return $page->getReport("", FALSE);
//#section_end#
?>