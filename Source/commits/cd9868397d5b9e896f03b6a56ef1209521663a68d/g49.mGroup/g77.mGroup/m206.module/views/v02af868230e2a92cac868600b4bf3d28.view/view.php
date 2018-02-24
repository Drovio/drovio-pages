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
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectAnalysisPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_analysisTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectAnalysisPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}


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

// Create module container
$attr = array();
$attr['projectID'] = $projectID;
$analysisContainer = $page->getModuleContainer($analysisModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectAnalysisContainer");
$page->append($analysisContainer);

// Return output
return $page->getReport();
//#section_end#
?>