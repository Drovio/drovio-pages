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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \DEV\Projects\project;
use \API\Profile\person;
use \API\Profile\tester;
use \API\Security\account;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	$pid= $_POST['pid'];
	$pwd = $_POST['pwd'];
	
	
	//Authenticate
	$username = person::getUsername();
	$status = account::authenticate($username,$pwd);
	if (!$status)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Authentication";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", $errFormNtf->getErrorMessage("err.authenticate"));
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	//If no error shown	
	$project = new project($pid);
	$info = $project->info();
	$prTitle = $info["title"];
	$prTitle = str_replace(" ","_",$prTitle);
	// Create Backup
	$suffix = "";
	$time = intval(date("G"));
	if ($time < 7)
		$time = "_night";
	else if ($time < 12)
		$time = "_start";
	else if ($time < 19)
		$time = "_noon";
	else
		$time = "_end";
	
	// example: 29jun2013_start
	$name = strtolower($prTitle."_".date("dMY").$time);
	$archive = systemRoot.tester::getTrunk()."/backup/".$name.".zip";
	$rootFolder = $project->getRootFolder();
	$contents = directory::getContentList(systemRoot."/".$rootFolder,TRUE);
	
	set_time_limit(600);
	$bStatus = zipManager::create($archive,$contents, TRUE, TRUE);
	if (!$bStatus)
	{
		$err_header = "Backup Status";
		$err = $errFormNtf->addErorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", $errFormNtf->getErrorMessage("err.required"));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();	


}
//#section_end#
?>