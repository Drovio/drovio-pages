<?php
//#section#[header]
// Module Declaration
$moduleID = 318;

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
importer::import("AEL", "Profiler");
importer::import("DEV", "Profiler");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \AEL\Profiler\logger as appLogger;
use \DEV\Projects\project;
use \DEV\Profiler\logger as coreLogger;

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

// Get project logger according to project type
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Logger
		$logger = coreLogger::getInstance();
		break;
	case 2:
		// Redback Modules Logger
		break;
	case 3:
		// Redback Web Engine Core Logger
		break;
	case 4:
		// Application Logger
		$logger = appLogger::getInstance($projectID);
		break;
	case 5:
		// Website Logger
		break;
	case 6:
		// Redback Website Template Logger
		break;
	case 7:
		// Redback Website Extension Logger
		break;
}

if (engine::isPost())
{
	// Get filename to remove
	$fname = engine::getVar('fname');
	
	// Remove logs
	if (isset($logger))
		$logger->removeLogByFile($fname);
	
	// Return reload action
	$pageContent = new MContent($moduleID);
	$pageContent->addReportAction($name = "file_logs.reload", $value = $fname);
	return $pageContent->getReport();
}
//#section_end#
?>