<?php
//#section#[header]
// Module Declaration
$moduleID = 121;

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
use \API\Developer\components\ebuilder\ebPackage;
use \API\Developer\components\ebuilder\ebLibrary;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Package Name
	$empty = inputValidator::checkNotset($_POST['library']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_library");
		$err = $errFormNtf->addErrorHeader("lib_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lib_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Package Name
	$empty = inputValidator::checkNotset($_POST['packageName']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_packageName");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$ebPkg = new ebPackage();
	$result = $ebPkg->create($_POST['library'], $_POST['packageName']);
	
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
$sForm = new simpleForm();


// Library Name
$ebLib = new ebLibrary();
$libraries = $ebLib->getList();
$title = moduleLiteral::get($moduleID, "lbl_library");
$input = $sForm->getResourceSelect($name = "library", $multiple = FALSE, $class = "", $libraries, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");

// Package
$title = moduleLiteral::get($moduleID, "lbl_packageName");
$input = $sForm->getInput($type = "text", $name = "packageName", $value = "", $class = "", $autofocus = FALSE);
$pkgRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");

// Header
$hd = moduleLiteral::get($moduleID, "hd_createPackage");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);

// Return the report
return $frame->build(moduleLiteral::get($moduleID, "hd_createPackage", FALSE), $moduleID, "createPackage", FALSE)->append($hdr)->append($libRow)->append($pkgRow)->getFrame();
//#section_end#
?>