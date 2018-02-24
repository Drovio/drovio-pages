<?php
//#section#[header]
// Module Declaration
$moduleID = 261;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\account;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Interactive\forms\switchButton;
use \DEV\Projects\project;

// Get request info
$projectID = engine::getVar('id');
$project = new project($projectID);
$projectInfo = $project->info();

// Initialize Content
$pageContent = new MContent($moduleID);

if (engine::isPost())
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	// Get project id
	if (empty($projectInfo))
		$hasError = TRUE;
	
	// Check authentication error
	if ($hasError)
		return $errorNtf->getReport();
	
	// Switch project status
	$currentStatus = $projectInfo['online'];
	$nextStatus = ($currentStatus == 0 ? 1 : 0);
	
	// Get project online
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_project_on_off");
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['status'] = $nextStatus;
	$status = $dbc->execute($q, $attr);
	
	// Add update status action
	$pageContent->addReportAction("project.updateStatus", $nextStatus);
	
	// Return report
	return switchButton::getReport($nextStatus);
}

// Build switch button form
$pageContent->build("", "projectStatusSwitcher");

// Get project status
$projectOnlineStatus = $projectInfo['online'];

// Add switch button
$sb = new switchButton();
$attr = array();
$attr['id'] = $projectID;
$statusSwitch = $sb->build("", $projectOnlineStatus)->engageModule($moduleID, $viewName = "setProjectStatus", $attr)->get();
$pageContent->append($statusSwitch);

return $pageContent->getReport();
//#section_end#
?>