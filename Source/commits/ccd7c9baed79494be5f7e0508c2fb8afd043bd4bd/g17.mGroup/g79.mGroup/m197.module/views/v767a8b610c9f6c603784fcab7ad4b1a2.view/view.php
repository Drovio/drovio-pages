<?php
//#section#[header]
// Module Declaration
$moduleID = 197;

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
use \API\Developer\ebuilder\wsManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check templateName
	$empty = (is_null($_POST['wsTitle']) || empty($_POST['wsTitle']));
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
	
	//No parametres error -> Continue
	$wsName = $_POST['wsTitle'];
	$wsDescription = $_POST['wseDescription'];
	 
	$wsManager = new wsManager();
	
	//Try to create new layout
	$success = $wsManager->create($wsName, $wsDescription);	
	// If error, show notification
	if (!$success )
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
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE); 
}

// Build Frame
$dFrame = new dialogFrame();
// Header
$title = moduleLiteral::get($moduleID, "lbl_createWebsite", FALSE);
$dFrame->build($title, $moduleID, "create", $background = TRUE);

// Create Content
$container = DOM::create();
$dFrame->append($container); 


$sForm = new simpleForm();

// templateName
$title = moduleLiteral::get($moduleID, "lbl_templateName"); 
$input = $sForm->getInput($type = "text", $name = "wsTitle", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($container, $row);

// templateDescription 
$title = moduleLiteral::get($moduleID, "lbl_templateDescription");
$input = $sForm->getTextarea($name = "wsDescription", $value = "", $class = "");
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($container, $row);

// return
return  $dFrame->getFrame();
//#section_end#
?>