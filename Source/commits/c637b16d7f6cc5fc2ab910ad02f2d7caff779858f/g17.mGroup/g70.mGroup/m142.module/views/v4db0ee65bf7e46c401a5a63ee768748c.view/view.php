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
use \API\Developer\ebuilder\extension;
use \API\Developer\ebuilder\extManager;
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
	// Clear report
	report::clear();
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check templateName
	$empty = (is_null($_POST['extensionName']) || empty($_POST['extensionName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_extensionName");
		$headerId = 'extensionName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'extensionName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// Check extensionCategory
	$empty = (is_null($_POST['extensionCategory']) || empty($_POST['extensionCategory']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_extensionCategory");
		$headerId = 'extensionCategory'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'extensionCategory'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$extensionName = $_POST['extensionName'];
	$extensionCategory = $_POST['extensionCategory'];
	$extensionDescription = $_POST['extensionDescription'];
	
	$extensionObject = new extension();	
	
	//Try to create new layout
	//$success = $extensionObject->create($extensionName, $extensionDescription, $extensionCategory);
	$success = $extensionObject->reCreate(16);	
	// If error, show notification
	if (!$success )
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create Extension";
					 		
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
$hd = moduleLiteral::get($moduleID, "newExtension", FALSE);

$frame->build($hd);

// Prepare data from db
$extensionCategorysArray = extManager::getAllCategories();

// Create form
$newLayoutFormObject = new simpleForm();
$newLayoutFormElement = $newLayoutFormObject->build($moduleID, "newExtension", $controls = TRUE);

// Append Form
DOM::append($container, $newLayoutFormElement->get());

// extensionName
$title = moduleLiteral::get($moduleID, "lbl_extensionName"); 
$input = $newLayoutFormObject->getInput($type = "text", $name = "extensionName", $value = "", $class = "", $autofocus = FALSE);
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// extensionCategory 
$resource = $extensionCategorysArray;
$title = moduleLiteral::get($moduleID, "lbl_extensionCategory ");
$input = $newLayoutFormObject->getResourceSelect($name = "extensionCategory ", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// extensionDescription 
$title = moduleLiteral::get($moduleID, "lbl_extensionDescription ");
$input = $newLayoutFormObject->getTextarea($name = "extensionDescription ", $value = "", $class = "");
$newLayoutFormObject->insertRow($title, $input, $required = FALSE, $notes = "");

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>