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
use \API\Developer\components\ebuilder\apiPackage;
use \API\Developer\components\ebuilder\apiLibrary;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;

// Create container
$container = DOM::create();

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
	
	// Check Namespace Name
	$empty = inputValidator::checkNotset($_POST['nsName']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		//$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err_header = "Name";
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
	
	// Get libName and packageName
	$packageNameArray = explode("::", $_POST['package']);
	$libName = $packageNameArray[0];
	$packageName = $packageNameArray[1];
/*
	$sdk = new sdkPackage();
	$result = $sdk->createNS($libName, $packageName, $_POST['nsName'], $_POST['parentNs']);
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
$createLib_formElement = $libForm->build($moduleID, "createNamespace", $controls = TRUE)->get();

// Header
//$hd = moduleLiteral::get($moduleID, "hd_createNamespace");
$hd = "Create New Namespace";
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

// Parent Namespace
//$title = moduleLiteral::get($moduleID, "lbl_namespace");
$title = "Namespace";
$nsInput = $libForm->getInput($type = "text", $name = "parentNs", $value = "", $class = "", $autofocus = FALSE);
$libForm->insertRow($title, $nsInput, $required = FALSE, $notes = "");

// Namespace
//$title = moduleLiteral::get($moduleID, "lbl_objectName");
$title = "Name";
$nsNameInput = $libForm->getInput($type = "text", $name = "nsName", $value = "", $class = "", $autofocus = FALSE);
$libForm->insertRow($title, $nsNameInput, $required = TRUE, $notes = "");

// Create Notification
$notification->append($container);
$promptNtf = $notification->get();

/*
$frame = new dialogFrame();
return $frame->build("Create new Package")->append($promptNtf)->getFrame();
*/

HTMLServerReport::clear();
HTMLServerReport::addContent($promptNtf, "popup");
return HTMLServerReport::get();
//#section_end#
?>