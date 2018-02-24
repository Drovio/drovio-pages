<?php
//#section#[header]
// Module Declaration
$moduleID = 387;

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
importer::import("DEV", "Projects");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
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
$page->build("", "templateDesigner");

// Action Attributes
$attr = array(); 
$attr['appID'] = $projectID;
$attr['id'] = $projectID;


// Build Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Explorer")->get();
$page->append($viewer);

// Create the developer's dynamic tabber for application components
$wide = new devTabber();
$ajaxWide = $wide->build($id = "redWIDE", $withBorder = FALSE)->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Library Viewer
$templateExplorer = DOM::create("div", "", "", "templateExplorer");
$splitter->appendToSide($templateExplorer);


// Template explorer container
$mContainer = $page->getModuleContainer($moduleID, "tplExplorer", $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
DOM::append($templateExplorer, $mContainer);

// Return output
return $page->getReport("", FALSE);
//#section_end#
?>