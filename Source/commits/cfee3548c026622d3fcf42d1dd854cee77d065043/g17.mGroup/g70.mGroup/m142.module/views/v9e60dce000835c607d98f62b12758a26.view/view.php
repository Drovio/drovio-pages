<?php
//#section#[header]
// Module Declaration
$moduleID = 142;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

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
use \API\Developer\ebuilder\extension;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\frames\windowFrame;

$extensionID  = $_GET['id'];
 
// Create container
$container = DOM::create();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();	
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
		
	// Check templateName
	$empty = (is_null($_POST['viewName']) || empty($_POST['viewName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_viewName");
		$headerId = 'viewName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'viewName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
		
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$viewName = $_POST['viewName'];
	$extensionID = $_POST['id'];
	
	$extensionObject = new extension();
	
	// Try to Load	
	$success = $extensionObject->load($extensionID);	
	if (!$success )
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load Extension";
	}
	else
	{	
		//create new view
		$success = $extensionObject->addView($viewName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create view";
		}
	}
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR  NOTIFICATION
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
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Build Notification
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "newView", FALSE);

$frame->build($hd);

// Create form
$newObjectFormBuilder = new simpleForm();
$newObjectFormElement = $newObjectFormBuilder->build($moduleID, "createView", $controls = TRUE)->get();

// Append Form
DOM::append($container, $newObjectFormElement);

// Template Id [Hidden]
$input = $newObjectFormBuilder->getInput($type = "hidden", "id", $extensionID , $class = "", $autofocus = FALSE);
$newObjectFormBuilder->append($input);

// viewName
$title = moduleLiteral::get($moduleID, "lbl_viewName"); 
$input = $newObjectFormBuilder->getInput($type = "text", $name = "viewName", $value = "", $class = "", $autofocus = FALSE);
$newObjectFormBuilder->insertRow($title, $input, $required = TRUE, $notes = "");



// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>