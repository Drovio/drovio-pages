<?php
//#section#[header]
// Module Declaration
$moduleID = 256;

// Inner Module Codes
$innerModules = array();
$innerModules['appSettings'] = 304;

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
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
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
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1: // Redback Core Market
	case 2: // Redback Pages Market
	case 3: // Redback Web Engine Core SDK Market
	case 5:	// Website Market
		$testerModuleID = NULL;
		break;
	case 4:
		// Application Tester Page
		$testerModuleID = $innerModules['appSettings'];
		break;
	case 6:
		// Redback Website Template Tester
		$testerModuleID = $innerModules['webSettings'];
		break;
	case 7:
		// Redback Website Extension Tester
		$testerModuleID = $innerModules['webSettings'];
		break;
}

if (!empty($testerModuleID))
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "lbl_marketSettingsTitle", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectMarketSettingsPage");
	
	// Append module container
	$attr = array();
	$attr['id'] = $projectID;
	$marketSettings = $page->getModuleContainer($testerModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectMarketSettingsContainer", $loading = FALSE, $preload = TRUE);
	$page->append($marketSettings);
}
else
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "lbl_marketSettingsTitle", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectMarketSettingsPage", TRUE);
}

// Return output
return $page->getReport();
//#section_end#
?>