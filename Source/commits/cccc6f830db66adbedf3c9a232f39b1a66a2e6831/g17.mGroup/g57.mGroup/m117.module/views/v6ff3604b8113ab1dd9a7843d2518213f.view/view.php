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
use \API\Developer\ebuilder\templateGroup;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\forms\inputValidator;
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
	
	// Check templateName
	$empty = (is_null($_POST['templateName']) || empty($_POST['templateName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_templateName");
		$headerId = 'templateName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'templateName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// Check templateType
	$empty = (is_null($_POST['templateType']) || empty($_POST['templateType']));
	if ($empty)
	{
		$has_error = TRUE;
			
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_templateType");
		$headerId = 'templateType'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'templateType'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	// Check templateGroup
	$empty = (is_null($_POST['templateGroup']) || empty($_POST['templateGroup']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_templateGroup");
		$headerId = 'templateGroup'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'templateGroup'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$templateName = $_POST['templateName'];
	$templateType = $_POST['templateType'];
	$templateGroup = $_POST['templateGroup'];
	$templateDescription = $_POST['templateDescription'];
	 
	$templateManager = new templateManager();
	
	//Try to create new layout
	$success = $templateManager->create($templateName, $templateDescription, $templateType, $templateGroup);	
	// If error, show notification
	if (!$success )
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create template";
					 		
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
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "addTemplate", FALSE);

$frame->build($hd);

// Prepare data from db
$siteTypeArray = template::getAllTypes();
$templateGroupsArray = templateGroup::getAllGroups();

// Create form
$newLayoutFormObject = new simpleForm();
$newLayoutFormElement = $newLayoutFormObject->build($moduleID, "newTemplate", $controls = TRUE);


// Append Form
DOM::append($container, $newLayoutFormElement->get());

// templateName
$title = moduleLiteral::get($moduleID, "lbl_templateName"); 
$input = $newLayoutFormObject->getInput($type = "text", $name = "templateName", $value = "", $class = "", $autofocus = FALSE);
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// templateType
$resource = array();
foreach ($siteTypeArray as $typeId => $typeValue)
{
	//Selector values
	$resource[$typeId] = $typeValue;//literal::get("sub::ebuilder::siteType", $siteType, FALSE);
}	
$title = moduleLiteral::get($moduleID, "lbl_templateType");
$input = $newLayoutFormObject->getResourceSelect($name = "templateType", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// templateGroup
$resource = $templateGroupsArray;
$title = moduleLiteral::get($moduleID, "lbl_templateGroup");
$input = $newLayoutFormObject->getResourceSelect($name = "templateGroup", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// templateDescription 
$title = moduleLiteral::get($moduleID, "lbl_templateDescription");
$input = $newLayoutFormObject->getTextarea($name = "templateDescription", $value = "", $class = "");
$newLayoutFormObject->insertRow($title, $input, $required = FALSE, $notes = "");

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>