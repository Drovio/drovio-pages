<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Html\HTMLContent;

// Build the content
$content = new HTMLContent();
$content->build("myPasswordManager");

// Initialize dbConnection
$dbc = new interDbConnection();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Password must be for current account
	$empty = (is_null($_POST['currentPassword']) || empty($_POST['currentPassword']));
	$valid = account::authenticate(account::getUsername(), $_POST['currentPassword']);
	if ($empty || !$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_password_currentPass");
		$err = $errFormNtf->addErrorHeader("lbl_password_h", $err_header);
		
		if ($empty)
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.required"));
		if (!$valid)
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}
	
	// Passwords must match
	$empty = (is_null($_POST['newPassword']) || empty($_POST['newPassword']));
	$match = ($_POST['newPassword'] == $_POST['newPassword2']);
	if ($empty || !$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_password_newPass");
		$err = $errFormNtf->addErrorHeader("lbl_password_h", $err_header);
		
		if ($empty)
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.required"));
		if (!$match)
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.validate"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Data
	$q = new dbQuery("968425505", "profile.account");
	$attr = array();
	$attr['id'] = account::getAccountID();
	$attr['password'] = hash("SHA256", $_POST['newPassword']);
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in creating the object, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_passwordManager");
		$err = $errFormNtf->addErrorHeader("lbl_pass_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_pass_desc", DOM::create("span", "Error Updating Password..."));
		return $errFormNtf->getReport();
	}
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE, TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


$sForm = new simpleForm();
$passwordEditForm = $sForm->build($moduleID, "passwordManager")->get();
$content->append($passwordEditForm);

// Current Password
$title = moduleLiteral::get($moduleID, "lbl_password_currentPass");
$input = $sForm->getInput($type = "password", $name = "currentPassword", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password
$title = moduleLiteral::get($moduleID, "lbl_password_newPass");
$input = $sForm->getInput($type = "password", $name = "newPassword", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password Again
$title = moduleLiteral::get($moduleID, "lbl_password_confirmNewPass");
$input = $sForm->getInput($type = "password", $name = "newPassword2", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $content->getReport($reportHolder);
//#section_end#
?>