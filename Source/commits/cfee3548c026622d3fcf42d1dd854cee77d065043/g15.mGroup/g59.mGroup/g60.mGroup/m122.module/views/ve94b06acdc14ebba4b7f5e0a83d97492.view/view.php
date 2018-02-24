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
use \API\Developer\components\ebuilder\apiLibrary;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
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
	
	// Check Library Name
	$empty = inputValidator::checkNotset($_POST['libName']);
	if ($empty)
	{
		$has_error = TRUE;
		// Header
		//$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err_header = "Library Name";
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
	
	//$result = apiLibrary::create($_POST['libName']);

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
$createLib_formElement = $libForm->build($moduleID, "createLibrary", $controls = TRUE)->get();

// Header
//$hd = moduleLiteral::get($moduleID, "hd_createLibrary");
$hd = "Create New Library";
$hd_span = DOM::create("span", $hd);
$hdr = DOM::create("h2");//, "", "", "lhd hd2");
DOM::append($hdr, $hd_span);
$notification->append($hdr);

// Append Form
DOM::append($container, $createLib_formElement);

// Library Name
//$title = moduleLiteral::get($moduleID, "lbl_libraryName");
$title = "Library Name";
$libNameInput = $libForm->getInput($type = "text", $name = "libName", $value = "", $class = "", $autofocus = FALSE);
$libForm->insertRow($title, $libNameInput, $required = TRUE, $notes = "");

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