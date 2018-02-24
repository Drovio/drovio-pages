<?php
//#section#[header]
// Module Declaration
$moduleID = 328;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \API\Profile\person;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

$accountID = engine::getVar("aid");
$dbc = new dbConnection();
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check account title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Passwords must match
	$match = ($_POST['password'] == $_POST['password2']);
	if (!empty($_POST['password']) && !$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		if (empty($_POST['password']))
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
		if (!$match)
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.validate"));
	}
	
	
	// Password must be for current account
	$valid = account::authenticate(account::getUsername(), $_POST['admin_password']);
	if (empty($_POST['admin_password'])|| !$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_adminAccountPassword");
		$err = $errFormNtf->addHeader($err_header);
		
		if (empty($_POST['admin_password']))
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
		if (!$valid)
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
	}

	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new account
	$dbq = module::getQuery($moduleID, "create_account");
	$attr = array();
	$attr['title'] = $_POST['title'];
	$attr['description'] = $_POST['description'];
	$attr['locked'] = ($_POST['locked'] == "on" ? 1 : 0);
	$attr['username'] = trim($_POST['username']);
	$attr['password'] = hash("SHA256", $_POST['password']);
	$attr['aid'] = account::getAccountID();
	$attr['pid'] = account::getPersonID();
	$result = $dbc->execute($dbq, $attr);
	
	if (!$result)
		return $errFormNtf->getReport();
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Create Module Page
$pageContent = new MContent($moduleID);
$pageContent->build("", "newAccount", TRUE);

// Add new account form
$form = new simpleForm();
$newAccountForm = $form->build()->engageModule($moduleID, "newAccount")->get();
$pageContent->append($newAccountForm);

// Account title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Account description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "description", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// Locked account
$title = moduleLiteral::get($moduleID, "lbl_lockedAccount");
$placeholder = moduleLiteral::get($moduleID, "lbl_lockedAccount_ph");
$input = $form->getInput($type = "checkbox", $name = "locked", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// Account username
$title = literal::dictionary("username");
$input = $form->getInput($type = "text", $name = "username", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// Account password
$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Account password again
$title = moduleLiteral::get($moduleID, "lbl_retypePass");
$input = $form->getInput($type = "password", $name = "password2", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Current account password
$title = moduleLiteral::get($moduleID, "lbl_adminAccountPassword");
$input = $form->getInput($type = "password", $name = "admin_password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport();
//#section_end#
?>