<?php
//#section#[header]
// Module Declaration
$moduleID = 122;

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
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \ESS\Protocol\server\HTMLServerReport;
use \API\Resources\forms\inputValidator;
use \API\Resources\literals\moduleLiteral;
use \API\Developer\components\ebuilder\apiObject;
use \API\Developer\components\ebuilder\apiLibrary;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;

// Create container
$container = DOM::create("div");

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	HTMLServerReport::clear();
	
	$has_error = FALSE;
	$errorNotification = new formErrorNotification();
	$errList = $errorNotification->build()->get();
	
	// Check Package Name
	$empty = inputValidator::checkNotset($_POST['package']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		//$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err_header = "Package";
		$errSubList = $errorNotification->addErrorHeader($id = "errh_".rand(), $err_header);
		
		// Descriptions
		// The error code is wrong?
		$description = $errorNotification->getMessage("error", "err.required");
		$errorNotification->addErrorDescription($errSubList, $id = "errd_".rand(), $description, $extra = ""); 
	}
	
	// Check Object Name
	$empty = inputValidator::checkNotset($_POST['objectName']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		//$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err_header = "Object Name";
		$errSubList = $errorNotification->addErrorHeader($id = "errh_".rand(), $err_header);
		
		// Descriptions
		// The error code is wrong?
		$description = $errorNotification->getMessage("error", "err.required");
		$errorNotification->addErrorDescription($errSubList, $id = "errd_".rand(), $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{
		return $errorNotification->getReport();
	}
	
	// Object Parameters
	/*
	$has_phpFile = !validator::_notset($_POST['phpFile']);
	$has_jsFile = !validator::_notset($_POST['jsFile']);
	$has_cssFile = !validator::_notset($_POST['cssFile']);
	*/
	// Get libName and packageName
	$packageNameArray = explode("::", $_POST['package']);
	$libName = $packageNameArray[0];
	$packageName = $packageNameArray[1];
/*
	$sdkObject = new sdkObject($libName, $packageName, $_POST['namespace']);
	$sdkObject->create($_POST['objectName'], $_POST['title'], $has_phpFile, $has_jsFile, $has_cssFile);
*/	
	$successNotification = new formNotification();
	$successNotification->build("success", TRUE);
	$message = $successNotification->getMessage("success", "success.save_success");
	$successNotification->append($message);
	
	return $successNotification->getReport();
}

// Build Notification
$notification = new notification();
$notification->build("default", FALSE, TRUE);

// Create form
$libForm = new simpleForm();
$createLib_formElement = $libForm->build($moduleID, "createObject", $controls = TRUE)->get();

// Header
//$hd = moduleLiteral::get($moduleID, "hd_createObject");
$hd = "Create New Object";
$hd_span = DOM::create("span", $hd);
$hdr = DOM::create("h2");//, "", "", "lhd hd2");
DOM::append($hdr, $hd_span);
$notification->append($hdr);

// Append Form
DOM::append($container, $createLib_formElement);

// Package
$packages = apiLibrary::getPackageList();
//$title = moduleLiteral::get($moduleID, "lbl_package");
$title = "Package";
$packSelect = $libForm->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libForm->insertRow($title, $packSelect, $required = TRUE, $notes = "");

// Namespace
//$title = moduleLiteral::get($moduleID, "lbl_namespace");
$title = "Namespace";
$nsInput = $libForm->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = FALSE);
$libForm->insertRow($title, $nsInput, $required = FALSE, $notes = "");

// Object Name
//$title = moduleLiteral::get($moduleID, "lbl_objectName");
$title = "Object Name";
$objNameInput = $libForm->getInput($type = "text", $name = "objectName", $value = "", $class = "", $autofocus = FALSE);
$libForm->insertRow($title, $objNameInput, $required = TRUE, $notes = "");

// Object Description
//$title = moduleLiteral::get($moduleID, "lbl_objectDescription");
$title = "Object Description";
$objDescInput = $libForm->getTextarea($name = "objectDescription", $value = "", $class = "");
$libForm->insertRow($title, $objDescInput, $required = FALSE, $notes = "");

/*
// Parameters
//$hd = moduleLiteral::get($moduleID, "lbl_objectParameters");
$hd = "Parameters";
$hd_span = DOM::create("span", $hd);
$hdr = DOM::create("h2");//, "", "", "lhd hd2");
DOM::append($hdr, $hd_span);
$notification->append($hdr);

// PHP File
$title = moduleLiteral::get($moduleID, "lbl_phpFile");
$fgroup = $libForm->get_form_input("input", $title, $name = "phpFile", "", $type = "checkbox", $class = "", $required = FALSE, $autofocus = FALSE);
$libForm->insert_to_body($fgroup['group']);
// Javascript File
$title = moduleLiteral::get($moduleID, "lbl_jsFile");
$fgroup = $libForm->get_form_input("input", $title, $name = "jsFile", "", $type = "checkbox", $class = "", $required = FALSE, $autofocus = FALSE);
$libForm->insert_to_body($fgroup['group']);

// CSS File
$title = moduleLiteral::get($moduleID, "lbl_cssFile");
$fgroup = $libForm->get_form_input("input", $title, $name = "cssFile", "", $type = "checkbox", $class = "", $required = FALSE, $autofocus = FALSE);
$libForm->insert_to_body($fgroup['group']);
*/

// Create Notification
$notification->append($container);
$promptNtf = $notification->get();

/*
$frame = new dialogFrame();
return $frame->build("Create new Library")->append($promptNtf)->getFrame();
*/

HTMLServerReport::clear();
HTMLServerReport::addContent($promptNtf, "popup");
return HTMLServerReport::get();
//#section_end#
?>