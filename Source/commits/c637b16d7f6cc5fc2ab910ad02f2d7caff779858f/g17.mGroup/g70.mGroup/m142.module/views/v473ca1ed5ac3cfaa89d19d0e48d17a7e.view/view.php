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

$extensionID  = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Package Name
	$empty = (is_null($_POST['packageName']) || empty($_POST['packageName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$extensionManager = new extension();
	$extensionManager->load($_POST['id']);
	$result = $extensionManager->addSrcPackage($_POST['packageName']);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_packageName");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", DOM::create("span", "Error creating package..."));
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
$frame->build("Create new Package", $moduleID, "createPackage", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "hd_createPackage");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Extension Id [Hidden]
$input = $sForm->getInput("hidden", "id", $extensionID);
$frame->append($input);

// Package
$title = moduleLiteral::get($moduleID, "lbl_objectName");
$input = $sForm->getInput($type = "text", $name = "packageName", $value = "", $class = "", $autofocus = FALSE);
$pkgRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($pkgRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>