<?php
//#section#[header]
// Module Declaration
$moduleID = 254;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\person;
use \API\Security\account;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	$pid = $_POST['pid'];
	$pwd = $_POST['pwd'];
	
	// Validate form
	if (!simpleForm::validate())
		$has_error = TRUE;
	
	// Authenticate account password
	$username = person::getUsername();
	$status = account::authenticate($username, $pwd);
	if (!$status)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Authentication";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", $errFormNtf->getErrorMessage("err.authenticate"));
	}
	
	// Check that it is not a red (system's) project
	$project = new project($pid);
	$projectInfo = $project->info();
	$redProject = ($projectInfo['projectType'] == 1 || $projectInfo['projectType'] == 2 || $projectInfo['projectType'] == 3);
	if ($redProject)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Red Project";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", "You cannot delete a Red Project!");
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Delete project from db
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "delete_project");
	$attr = array();
	$attr['pid'] = $pid;
	$result = $dbc->execute($q, $attr);
	
	// Remove keys relative to project
	$q = module::getQuery($moduleID, "remove_project_keys");
	$attr = array();
	$attr['pid'] = $pid;
	$result = $dbc->execute($q, $attr);
	
	// Remove project folder
	$project->remove();
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();	


}
//#section_end#
?>