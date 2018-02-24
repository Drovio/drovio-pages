<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;

$dbc = new dbConnection();
if ($_SERVER['REQUEST_METHOD'] == "POST")
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
		$err = $errFormNtf->addErrorHeader("title_h", $err_header);
		$errFormNtf->addErrorDescription($err, "title_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Passwords must match
	$match = ($_POST['password'] == $_POST['password2']);
	if (!empty($_POST['password']) && !$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addErrorHeader("title_h", $err_header);
		if (empty($_POST['password']))
			$errFormNtf->addErrorDescription($err, "title_desc", $errFormNtf->getErrorMessage("err.required"));
		if (!$match)
			$errFormNtf->addErrorDescription($err, "title_desc", $errFormNtf->getErrorMessage("err.validate"));
	}
	
	
	// Password must be for current account
	$valid = account::authenticate(account::getUsername(), $_POST['admin_password']);
	if (empty($_POST['admin_password'])|| !$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_password_currentPass");
		$err = $errFormNtf->addErrorHeader("lbl_password_h", $err_header);
		
		if (empty($_POST['admin_password']))
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.required"));
		if (!$valid)
			$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}

	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	if (!empty($_POST['password']))
	{
		// Update account password
		$dbq = module::getQuery($moduleID, "update_account_password");
		$attr = array();
		$attr['password'] = hash("SHA256", $_POST['password']);
		$attr['id'] = $_POST['acc_id'];
		$dbc->execute($dbq, $attr);
	}
	
	// Update account info
	$dbq = module::getQuery($moduleID, "update_account_info");
	$attr = array();
	$attr['title'] = $_POST['title'];
	$attr['description'] = $_POST['description'];
	$attr['locked'] = ($_POST['locked'] == "on" ? 1 : 0);
	$attr['aid'] = $_POST['acc_id'];
	$dbc->execute($dbq, $attr);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Create Module Page
$pageContent = new MContent($moduleID);
$pageContent->build("", "accountManager", TRUE);


// Get managed accounts
$dbq = module::getQuery($moduleID, "get_managed_accounts");
$attr = array();
$attr['parent'] = account::getAccountID();
$result = $dbc->execute($dbq, $attr);

$toggler = new toggler();

// Show each account and set form for changing title, description and password
$accountContainer = HTML::select(".accountEditor")->item(0);
while ($account = $dbc->fetch($result))
{
	// Toggler Header
	$header = DOM::create("div", $account['title']);
	
	$form = new simpleForm();
	$body = $form->build($moduleID, "accountManager")->get();
	
	// Account id
	$input = $form->getInput($type = "hidden", $name = "acc_id", $value = $account['id'], $class = "", $autofocus = FALSE);
	$form->append($input);
	
	// Account title
	$title = literal::dictionary("title");
	$input = $form->getInput($type = "text", $name = "title", $value = $account['title'], $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	// Account description
	$title = literal::dictionary("description");
	$input = $form->getTextarea($name = "description", $value = $account['description'], $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = FALSE, $notes = "");
	
	// Locked account
	$title = moduleLiteral::get($moduleID, "lbl_lockedAccount");
	$input = $form->getInput($type = "checkbox", $name = "locked", $value = "", $class = "", $autofocus = FALSE);
	if ($account['locked'])
		DOM::attr($input, "checked", "checked");
	$form->insertRow($title, $input, $required = FALSE, $notes = "");
	
	// Account password
	$title = literal::dictionary("password");
	$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	// Account password again
	$title = moduleLiteral::get($moduleID, "lbl_retypePass");
	$input = $form->getInput($type = "password", $name = "password2", $value = "", $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	
	// Current account password
	$title = moduleLiteral::get($moduleID, "lbl_adminAccountPassword");
	$input = $form->getInput($type = "password", $name = "admin_password", $value = "", $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	
	$togglerItem = $toggler->build("tga".$account['id'], $header, $body, $open = FALSE)->get();
	DOM::append($accountContainer, $togglerItem);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>