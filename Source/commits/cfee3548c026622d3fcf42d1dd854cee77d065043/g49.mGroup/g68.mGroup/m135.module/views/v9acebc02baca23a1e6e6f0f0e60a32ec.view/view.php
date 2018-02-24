<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

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
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\geoloc\locale;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['version']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_publishVersion");
		$err = $errFormNtf->addErrorHeader("version_h", $err_header);
		$errFormNtf->addErrorDescription($err, "version_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	if (empty($_POST['description']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_publishDescription");
		$err = $errFormNtf->addErrorHeader("versionDescription_h", $err_header);
		$errFormNtf->addErrorDescription($err, "versionDescription_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Initialize application
	$appID = $_POST['appID'];
	$devApp = new application($appID);
	
	// Publish application
	$result = $devApp->publish($_POST['version'], $_POST['description']);

	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error publishing application..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_appPublisherTitle");
$frame->build($title, $moduleID, "appPublisher", TRUE);

// Validate and Load application data
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$frame->append($errorMessage);
	return $frame->getFrame();
}

// Get application
$devApp = new application($appID);
$form = new simpleForm();

// Application id
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Publish Version
$title = moduleLiteral::get($moduleID, "lbl_publishVersion");
$input = $form->getInput($type = "text", $name = "version", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "placeholder", "1.0, 1.0a, 1.1 etc.");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes);
$frame->append($formRow);

// Publish Description
$title = moduleLiteral::get($moduleID, "lbl_publishDescription");
$input = $form->getTextarea($name = "description", $value = "", $class = "", $autofocus = FALSE);
DOM::attr($input, "placeholder", "Write a version description...");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes);
$frame->append($formRow);


// Return the report
return $frame->getFrame();
//#section_end#
?>