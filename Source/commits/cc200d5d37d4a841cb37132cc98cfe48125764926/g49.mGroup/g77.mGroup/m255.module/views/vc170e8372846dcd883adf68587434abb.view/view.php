<?php
//#section#[header]
// Module Declaration
$moduleID = 255;

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
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
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

// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Statistics
		$statisticsModuleID = $innerModules['coreStatistics'];
		break;
	case 2:
		// Redback Modules Statistics
		$statisticsModuleID = $innerModules['modulesStatistics'];
		break;
	case 3:
		// Redback Web Engine Core SDK Statistics
		$statisticsModuleID = $innerModules['webEngineStatistics'];
		break;
	case 4:
		// Application Statistics Page
		$statisticsModuleID = $innerModules['appStatistics'];
		break;
	case 5:
		// Website Statistics Page
		$statisticsModuleID = $innerModules['websiteStatistics'];
		break;
	case 6:
		// Redback Website Template Statistics
		$statisticsModuleID = $innerModules['webTemplateStatistics'];
		break;
	case 7:
		// Redback Website Extension Statistics
		$statisticsModuleID = $innerModules['webExtensionStatistics'];
		break;
}

if (!empty($statisticsModuleID))
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectStatisticsPage");
	
	// Append module container
	$attr = array();
	$attr['id'] = $projectID;
	$marketSettings = $page->getModuleContainer($statisticsModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectStatisticsContainer", $loading = FALSE, $preload = TRUE);
	$page->append($marketSettings);
}
else
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectStatisticsPage", TRUE);
}

// Return output
return $page->getReport();
//#section_end#
?>