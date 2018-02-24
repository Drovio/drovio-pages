<?php
//#section#[header]
// Module Declaration
$moduleID = 117;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

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
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\windowFrame;

// Create container
$container = DOM::create();

$themeName = $_GET['objectName'];
$templateID = $_GET['templateId'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
		
	// Check templateId
	$empty = (is_null($_POST['templateId']) || empty($_POST['templateId']));
	if ($empty)
	{
		$has_error = TRUE;
				
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Internal error Reload";
	}
	
	// Check pageStructure
	$empty = (is_null($_POST['theme']) || empty($_POST['theme']));
	if ($empty)
	{
		$has_error = TRUE;
				
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Internal error Reload";
	}
	
	// If error, show notification
	if ($has_error)
	{
		// SUCCESS NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
		
		return $errorNotification->getReport(FALSE);
	}
	
	//No parametres error -> Continue
	$themeName = $_POST['theme'];
	$templateID = $_POST['templateId'];
	
	
	// Try to load Object
	$templateManager = new templateManager();
	$templateObject = $templateManager->getTemplate($templateID);		
	
	// Try to Load	
	$success = $templateObject->load($templateID);	
	if (!$success )
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load template";
	}
	else
	{	
		//Try to create new layout
		$success = $templateObject->deleteTheme($themeName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not delete theme";
		}
	}
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= "SUCCESS NOTIFICATION : ".$success;
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Build Notification
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "deleteTheme", FALSE);

$frame->build($hd);

// Create form
$sForm = new simpleForm();
$sForm->build($moduleID, "deleteTheme", $controls = TRUE);

// Append Form
DOM::append($container, $sForm->get());

// Template Id [Hidden]
$input = $sForm->getInput("hidden", "templateId", $templateID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Page Structure Name [Hidden]
$input = $sForm->getInput("hidden", "theme", $themeName, $class = "", $autofocus = FALSE);
$sForm->append($input);

$confiramtionMsg = DOM::create('span', 'Really ????? : '.$themeName);
$sForm->append($confiramtionMsg);

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>