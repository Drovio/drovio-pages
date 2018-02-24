<?php
//#section#[header]
// Module Declaration
$moduleID = 261;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\person;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	// Validate form
	if (!simpleForm::validate())
		$hasError = TRUE;
	
	// Authenticate account
	$username = person::getUsername();
	$password = $_POST['password'];
	if (!account::authenticate($username, $password))
	{
		$hasError = TRUE;
		$hd = moduleLiteral::get($moduleID, "authentication_error_header");
		$header = $errorNtf->addErrorHeader("err", $hd);
		$errorNtf->addErrorDescription($header, "errDesc", $errFormNtf->getErrorMessage("err.authenticate"));
	}
	
	// Check authentication error
	if ($hasError)
		return $errorNtf->getReport();
	
	// Get project id
	$projectID = $_POST['id'];
	$project = new project($projectID);
	$projectInfo = $project->info();
	if (is_null($projectInfo))
		$hasError = TRUE;
	
	// Check authentication error
	if ($hasError)
		return $errorNtf->getReport();
	
	// Get project online
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_project_on_off");
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['status'] = 0;
	$status = $dbc->execute($q, $attr);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}
return FALSE;
//#section_end#
?>