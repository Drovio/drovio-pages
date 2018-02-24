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
importer::import("API", "Geoloc");
importer::import("API", "Resources");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Profiler");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Geoloc\datetimer;
use \API\Resources\filesystem\directory;
use \UI\Modules\MPage;
use \AEL\Profiler\logger as appLogger;
use \DEV\Projects\project;
use \DEV\Profiler\logger as coreLogger;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$page->build("File Logs  | ".$projectTitle, "projectFileLogsPage", TRUE);
$logListContainer = HTML::select(".projectFileLogs .panel.list")->item(0);

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
		$logger = appLogger::getInstance();
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

// Get logs
if (isset($logger))
{
	$fileLogs = $logger->getLogFiles();
	krsort($fileLogs);
}

// List all log files
foreach ($fileLogs as $fileName => $fileInfo)
{
	// Get file name (without extension)
	$fileName_noExtension = str_replace(".".$fileInfo['extension'], "", $fileInfo['name']);
	
	// Package Row
	$fRow = DOM::create("div", "", "", "fRow");
	DOM::append($logListContainer, $fRow);
	NavigatorProtocol::staticNav($fRow, "", "", "", "lnavGroup", $display = "none");
	
	$attr = array();
	$attr['fname'] = $fileName_noExtension;
	$actionFactory->setModuleAction($fRow, $moduleID, "logContext", ".projectFileLogs .panel.context", $attr, $loading = TRUE);
	
	// Date modified
	$date = datetimer::live($fileInfo['lastModified']);
	$fModified = DOM::create("div", $date, "", "fModified");
	DOM::append($fRow, $fModified);
	
	// Set release info
	$fTitle = DOM::create("h4", $fileName_noExtension, "", "fTitle");
	DOM::append($fRow, $fTitle);
	
	// File size
	$fileSize = directory::formatBytes($fileInfo['size'], $precision = 2);
	$fSize = DOM::create("div", $fileSize, "", "fSize");
	DOM::append($fRow, $fSize);
}

return $page->getReport();
//#section_end#
?>