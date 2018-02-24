<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get project id
	$projectID = $_POST['pid'];
	if (empty($projectID))
		$has_error = TRUE;
	
	// Check Theme
	if (empty($_POST['status']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_projectStatus");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, "You must select a status!");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update project status
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "review_project");
	$attr = array();
	$attr['pid'] = $_POST['pid'];
	$attr['version'] = $_POST['version'];
	$attr['comments'] = $_POST['comments'];
	$attr['status'] = $_POST['status'];
	$attr['raid'] = account::getAccountID();
	$attr['time'] = time();
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in creating the folder, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_projectStatus");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating project status..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	// Report action
	$errFormNtf->addReportAction("rvProjects.reload");
	
	return $succFormNtf->getReport();
}
//#section_end#
?>