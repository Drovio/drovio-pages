<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

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
use \API\Developer\resources\layouts\systemLayout;
use \API\Developer\resources\layouts\ebuilderLayout;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\windowFrame;

// Create container
$container = DOM::create();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Package Name
	$empty = (is_null($_POST['group']) || empty($_POST['group']));
	if ($empty)
	{				
		// Header
		$header = moduleLiteral::get($moduleID, "hdr_layoutGroup");
		$headerId = 'group'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'group'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");	
	
		$has_error = TRUE;		
	}
	
	// Check Object Name
	$empty = (is_null($_POST['name']) || empty($_POST['name']));
	if ($empty)
	{
		// Header
		$header = moduleLiteral::get($moduleID, "hdr_layoutName");
		$headerId = 'name'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'name'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");	
	
		$has_error = TRUE;	
	}
	
	// If error, show notification
	if ($has_error)
	{		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$group = $_POST['group'];
	$layoutName = $_POST['name'];
	
	switch($group)
	{
		case 'ebuilder' :
			$layoutManager = new ebuilderLayout();
			break;
		case 'system' :
			$layoutManager = new systemLayout();
			break;
		default :
			break;	
	}
	
	//Try to create new layout
	$success = $layoutManager->create($layoutName);	
	// If error, show notification
	if (!$success )
	{	
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create layout";
					 		
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
	$message = $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Build Frame
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "createLayout", FALSE);

$frame->build($hd);

// Create form
$newLayoutFormObject = new simpleForm();
$newLayoutFormElement = $newLayoutFormObject->build($moduleID, "newLayout", $controls = TRUE)->get();
// Append Form
DOM::append($container, $newLayoutFormElement);

// Group
$groupOptions = array();
//Selector values
$groupOptions["system"] = moduleLiteral::get($moduleID, "hdr_systemLayouts", FALSE);
$groupOptions["ebuilder"] = moduleLiteral::get($moduleID, "hdr_ebuilderLayouts", FALSE);
$title = moduleLiteral::get($moduleID, "hdr_layoutGroup");
$input = $newLayoutFormObject->getResourceSelect($name = "group", $multiple = FALSE, $class = "", $groupOptions, $selectedValue = "");
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Object Name
$title = moduleLiteral::get($moduleID, "hdr_layoutName");;
$input = $newLayoutFormObject->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>