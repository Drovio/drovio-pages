<?php
//#section#[header]
// Module Declaration
$moduleID = 227;

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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
//#section_end#
//#section#[code]
use \DEV\BugTracker\bugTracker;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Html\HTMLContent;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	$empty = (is_null($_POST['bid']) || empty($_POST['bid']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_wsTitle");
		$headerId = 'wsTitle'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'wsTitle'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{	
		return $formErrorNotification->getReport();
	}
	
	$bugger = new bugTracker($_POST['pid']);
	
	// Save File 
	$success = $bugger->assignToBug($_POST['bid']);	
	if($success)
	{
		// SUCCESS NOTIFICATION
		$successNotification = new formNotification();
		$successNotification->build("success");
		
		// Description
		$message= $successNotification->getMessage( "success", "success.save_success");
		$successNotification->appendCustomMessage($message);
		
		return $successNotification->getReport(FALSE);
	}
	else
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create website";
					 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);
	}
	
}
//#section_end#
?>