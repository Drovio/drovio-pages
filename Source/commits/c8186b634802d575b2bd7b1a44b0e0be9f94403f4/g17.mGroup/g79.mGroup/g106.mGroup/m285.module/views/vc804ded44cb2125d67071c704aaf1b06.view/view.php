<?php
//#section#[header]
// Module Declaration
$moduleID = 285;

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
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\Websites\pages\wsPageManager;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check name
	$empty = (is_null($_POST['name']) || empty($_POST['name']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_name");
		$err_header = $formErrorNotification->addErrorHeader("nameErrorHeader", $header);
		$errFormNtf->addErrorDescription($err_header, "libName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
	{	
		return $formErrorNotification->getReport();
	}
	
	// Create folder
	$pMan = new wsPageManager($_POST['id']);
	$parent = ($_POST['parent'] == -1 ? "" : $_POST['parent']);
	$success = $pMan->createFolder($parent, $_POST['name']);
	
	// If error, show notification
	if (!$success)
	{
		// Header
		$header = "";
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, "Error", $extra = "Error creating folder.");
		return $formErrorNotification->getReport();
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	return $successNotification->getReport();
}
//#section_end#
?>