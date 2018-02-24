<?php
//#section#[header]
// Module Declaration
$moduleID = 206;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesAnalysis'] = 251;
$innerModules['coreAnalysis'] = 250;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
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
		// Redback Core Analysis
		$analysisModuleID = $innerModules['coreAnalysis'];
		break;
	case 2:
		// Redback Modules Analysis
		$analysisModuleID = $innerModules['modulesAnalysis'];
		break;
	case 3:
		// Redback Web Engine Core SDK Analysis
		$analysisModuleID = $innerModules['webEngineAnalysis'];
		break;
	case 4:
		// Application Analysis Page
		$analysisModuleID = $innerModules['appAnalysis'];
		break;
	case 5:
		// Website Analysis Page
		$analysisModuleID = $innerModules['websiteAnalysis'];
		break;
	case 6:
		// Redback Website Template Analysis
		$analysisModuleID = $innerModules['webTemplateAnalysis'];
		break;
	case 7:
		// Redback Website Extension Analysis
		$analysisModuleID = $innerModules['webExtensionAnalysis'];
		break;
}

if (!empty($analysisModuleID))
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($projectTitle." | ".$title, "projectAnalysisPage");
	
	// Append module container
	$attr = array();
	$attr['id'] = $projectID;
	$analysisContainer = $page->getModuleContainer($analysisModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectAnalysisContainer", $loading = FALSE, $preload = TRUE);
	$page->append($analysisContainer);
}
else
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($projectTitle." | ".$title, "projectAnalysisPage", TRUE);
}

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>