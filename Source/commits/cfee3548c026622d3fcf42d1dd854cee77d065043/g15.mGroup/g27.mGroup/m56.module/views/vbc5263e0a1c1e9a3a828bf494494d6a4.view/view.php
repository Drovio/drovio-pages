<?php
//#section#[header]
// Module Declaration
$moduleID = 56;

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
importer::import("API", "Content");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Content\validation\validator;
use \API\Developer\content\resources\mapping;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkObject;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\frames\windowFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
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
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err = $errFormNtf->addErrorHeader("objName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "objName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Object Parameters
	$has_phpFile = !validator::_notset($_POST['phpFile']);
	$has_jsFile = !validator::_notset($_POST['jsFile']);
	$has_cssFile = !validator::_notset($_POST['cssFile']);
	
	// Get libName and packageName
	$packageNameArray = explode("::", $_POST['package']);
	$libName = $packageNameArray[0];
	$packageName = $packageNameArray[1];

	$sdkObject = new sdkObject($libName, $packageName, $_POST['namespace']);
	$result = $sdkObject->create($_POST['objectName'], $_POST['objectDesc'], $has_phpFile, $has_jsFile, $has_cssFile);
	
	// If there is an error in creating the object, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err = $errFormNtf->addErrorHeader("objName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "objName_desc", DOM::create("span", "Error creating object..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$frame->build("Create new Object", $moduleID, "createObject", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "hd_createObject");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Library Name
$sdkLib = new sdkLibrary();
$packages = $sdkLib->getPackageList();
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $sForm->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $sForm->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($nsRow);

// Object Name
$title = moduleLiteral::get($moduleID, "lbl_objectName");
$input = $sForm->getInput($type = "text", $name = "objectName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($objRow);

// Object Description
$title = moduleLiteral::get($moduleID, "lbl_objectDescription");
$input = $sForm->getTextarea($name = "objectDesc", $value = "", $class = "");
$objDescRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($objDescRow);

// Parameters
$hd = moduleLiteral::get($moduleID, "lbl_objectParameters");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// PHP File
$title = moduleLiteral::get($moduleID, "lbl_phpFile");
$input = $sForm->getInput($type = "checkbox", $name = "phpFile", $value = "", $class = "", $autofocus = FALSE);
$phpRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($phpRow);

// Javascript File
$title = moduleLiteral::get($moduleID, "lbl_jsFile");
$input = $sForm->getInput($type = "checkbox", $name = "jsFile", $value = "", $class = "", $autofocus = FALSE);
$jsRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($jsRow);

// CSS File
$title = moduleLiteral::get($moduleID, "lbl_cssFile");
$input = $sForm->getInput($type = "checkbox", $name = "cssFile", $value = "", $class = "", $autofocus = FALSE);
$cssRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($cssRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>