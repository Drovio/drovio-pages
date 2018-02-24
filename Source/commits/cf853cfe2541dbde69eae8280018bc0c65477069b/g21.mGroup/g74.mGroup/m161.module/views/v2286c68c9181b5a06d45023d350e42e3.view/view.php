<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

if (engine::isPost())
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
	
	// Update account password
	$status = account::updatePassword($_POST['currentPassword'], $_POST['newPassword']);
	
	// If there is an error in creating the object, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_passwordManager");
		$err = $errFormNtf->addErrorHeader("lbl_pass_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_pass_desc", DOM::create("span", "Error Updating Password..."));
		return $errFormNtf->getReport();
	}
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the content
$content = new MContent($moduleID);
$content->build("", "myPasswordManager");

$form = new simpleForm();
$passwordEditForm = $form->build($moduleID, "passwordManager")->get();
$content->append($passwordEditForm);

// Current Password
$title = moduleLiteral::get($moduleID, "lbl_password_currentPass");
$input = $form->getInput($type = "password", $name = "currentPassword", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password
$title = moduleLiteral::get($moduleID, "lbl_password_newPass");
$input = $form->getInput($type = "password", $name = "newPassword", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password Again
$title = moduleLiteral::get($moduleID, "lbl_password_confirmNewPass");
$input = $form->getInput($type = "password", $name = "newPassword2", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $content->getReport();
//#section_end#
?>