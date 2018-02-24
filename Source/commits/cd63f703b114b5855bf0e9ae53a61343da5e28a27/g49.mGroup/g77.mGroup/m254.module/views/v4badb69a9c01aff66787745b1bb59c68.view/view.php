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
importer::import("DEV", "Projects");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \API\Literals\moduleLiteral;
use \DEV\Projects\project;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	$pid = $_POST['pid'];
	$title = $_POST['title'];
	$name = $_POST['name'];
	$name = str_replace(" ", "_", $name);
	$desc = $_POST['description'];
	
	//Check Title 
	if (empty( $title))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Project Title";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", $errFormNtf->getErrorMessage("err.required"));
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	//If no error shown	
	$project = new project($pid);
	$project->updateInfo($title, $desc);
	$project->setName($name);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
		
}
//#section_end#
?>