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
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

$extensionID = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();	
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Package Name
	$empty = (is_null($_POST['package']) || empty($_POST['package']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}	
	
	
	// Check Object Name
	$empty = (is_null($_POST['objectName']) || empty($_POST['objectName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_objectName");
		$headerId = 'objectName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'objectName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
		
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	//$isClass = isset($_POST['isClass']) ? 1 : 0;
	$extensionObject = new extension();
	
	// Try to Load	
	$success = $extensionObject->load($_POST['id']);	
	if (!$success )
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load Extension";
	}
	else
	{	
		//create new page	
		$success = $extensionObject->addSrcObject($_POST['package'], $_POST['objectName'], $_POST['namespace']);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create script";
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

// Build the frame
$frame = new dialogFrame();
$frame->build(moduleLiteral::get($moduleID, "hd_createObject", FALSE), $moduleID, "createPhpScript", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "hd_createObject");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Extension Id [Hidden]
$input = $sForm->getInput("hidden", "id", $extensionID);
$frame->append($input);

// Package Name
$extensionManager = new extension();
$extensionManager->load($extensionID);
$packages = $extensionManager->getPackageList();

$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $sForm->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $sForm->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($nsRow);

// ObjectName
$title = moduleLiteral::get($moduleID, "lbl_objectName"); 
$input = $sForm->getInput($type = "text", $name = "objectName", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($row);

// Class
//$title = moduleLiteral::get($moduleID, "lbl_class");
//$input = $newObjectFormBuilder->getInput($type = "checkbox", $name = "isClass", $value = "", $class = "", $autofocus = TRUE);
//$row = $newObjectFormBuilder->buildRow($title, $input, $required = FALSE, $notes = "");
//$frame->append($row);

// Return the report
return $frame->getFrame();
//#section_end#
?>