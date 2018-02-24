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
	
	if (empty($_POST['fullname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_appFullName");
		$err = $errFormNtf->addErrorHeader("fullName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "fullName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Update application data
	$appID = $_POST['appID'];
	$applicationData = appManager::getApplicationData($appID);
	
	$fullName = $_POST['fullname'];
	$name = (empty($_POST['name']) ? $applicationData['name'] : $_POST['name']);
	$tags = $_POST['tags'];
	$privacy = $_POST['privacy'];
	$description = $_POST['description'];
	$result = appManager::updateApplicationData($appID, $fullName, $privacy, $tags, $name, $description);

	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = "Application Info";
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error updating application..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Validate and Load application data
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
$dialogPrefix = (empty($applicationData['name']) ? "" : $applicationData['name']." - ");

// Build the frame
$frame = new dialogFrame();
$title = $dialogPrefix.moduleLiteral::get($moduleID, "lbl_appInfoTitle", FALSE);
$frame->build($title, $moduleID, "appInfo", TRUE);

if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$frame->append($errorMessage);
	return $frame->getFrame();
}

$form = new simpleForm();

// Application id
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Application Full name
$title = moduleLiteral::get($moduleID, "lbl_appFullName");
$notes = moduleLiteral::get($moduleID, "lbl_appFullNameNotes");
$input = $form->getInput($type = "text", $name = "fullname", $value = $applicationData['fullName'], $class = "", $autofocus = FALSE);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes);
$frame->append($formRow);

if (empty($applicationData['name']))
{
	// Application Unique name
	$title = moduleLiteral::get($moduleID, "lbl_appName");
	$notes = moduleLiteral::get($moduleID, "lbl_appNameNotes");
	$input = $form->getInput($type = "text", $name = "name", $value = $applicationData['name'], $class = "", $autofocus = FALSE);
	$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
	$frame->append($formRow);
}

// Application Tags
$title = moduleLiteral::get($moduleID, "lbl_appTags");
$input = $form->getInput($type = "text", $name = "tags", $value = $applicationData['tags'], $class = "", $autofocus = FALSE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($formRow);

// Application Privacy
$appPrivacy = $applicationData['scope'];
$title = moduleLiteral::get($moduleID, "lbl_appPrivacy");
$notes = moduleLiteral::get($moduleID, "lbl_appPrivacyNotes");
$options = array();
$optTitle = moduleLiteral::get($moduleID, "lbl_appPrivacyOptPrivate");
$options[] = $form->getOption($optTitle, $value = "private", $selected = ($appPrivacy == "private"));
$optTitle = moduleLiteral::get($moduleID, "lbl_appPrivacyOptPublic");
$options[] = $form->getOption($optTitle, $value = "public", $selected = ($appPrivacy == "public"));
$input = $form->getSelect($name = "privacy", $multiple = FALSE, $class = "", $options);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes);
$frame->append($formRow);

// Application Description
$title = moduleLiteral::get($moduleID, "lbl_appDescription");
$ph = moduleLiteral::get($moduleID, "lbl_appDescPh", FALSE);
$input = $form->getTextarea($name = "description", $value = $applicationData['description'], $class = "", $autofocus = FALSE);
DOM::attr($input, "placeholder", $ph);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($formRow);


// Return the report
return $frame->getFrame();
//#section_end#
?>