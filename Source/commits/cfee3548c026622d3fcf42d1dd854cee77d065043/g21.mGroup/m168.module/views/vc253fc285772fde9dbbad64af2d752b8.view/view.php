<?php
//#section#[header]
// Module Declaration
$moduleID = 168;

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
use \API\Resources\literals\literal;
use \API\Security\account;
use \UI\Html\HTMLModulePage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");
$actionFactory = $page->getActionFactory();

// If user is already logged in, go to my settings
if (account::validate())
	return $actionFactory->getReportRedirect("/settings/", "my", $formSubmit = FALSE);
	
$dbc = new interDbConnection();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	// Create Notification
	$errFormNtf = new formErrorNotification();
	$errFormNtf->build();
	
	if (empty($_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_newPassword");
		$err = $errFormNtf->addErrorHeader("lbl_newPassword_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_newPassword_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	if ($_POST['password'] != $_POST['password_verify'])
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "password_verify");
		$err = $errFormNtf->addErrorHeader("lbl_newPasswordVerify_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_newPasswordVerify_desc", $errFormNtf->getErrorMessage("err.validate"));
	}
	
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	// Verify account id with reset
	$q = new dbQuery("819652838", "profile.account");
	$attr = array();
	$attr['reset'] = $_POST['rs'];
	$result = $dbc->execute($q, $attr);
	$account = $dbc->fetch($result);
	
	if ($account['id'] != $_POST['acc'])
		return $errFormNtf->getReport();
		
	// Erase reset id
	$q = new dbQuery("1968348956", "profile.account");
	$attr = array();
	$attr['reset'] = "";
	$attr['accountID'] = $account['id'];
	$result = $dbc->execute($q, $attr);
	
	// Update account password
	$q = new dbQuery("968425505", "profile.account");
	$attr = array();
	$attr['id'] = $account['id'];
	$attr['password'] = hash("SHA256", $_POST['password']);
	$result = $dbc->execute($q, $attr);
	
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_newPassword");
		$err = $errFormNtf->addErrorHeader("lbl_pass_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_pass_desc", DOM::create("span", "Error Updating Password..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE); 
$page->build($pageTitle, "resetPassword");

$pageHeaderContent = moduleLiteral::get($moduleID, "lbl_pageTitle");
$pageHeader = DOM::create("h2", $pageHeaderContent);
$page->appendToSection("mainContent", $pageHeader);

// Validate hash id
$resetID = $_GET['rs'];
	
// Get the account by the reset id
$q = new dbQuery("819652838", "profile.account");
$attr = array();
$attr['reset'] = $resetID;
$result = $dbc->execute($q, $attr);
$account = $dbc->fetch($result);

if (is_null($account))
{
	$title = moduleLiteral::get($moduleID, "lbl_notValid");
	$header = DOM::create("h3", $title);
	$page->appendToSection("mainContent", $header);
	return $page->getReport();
}


// Create form
$form = new simpleForm("resetPassword");
$resetForm = $form->build($moduleID)->get();
$page->appendToSection("mainContent", $resetForm);

// Safety values
$input = $form->getInput($type = "hidden", $name = "rs", $value = $resetID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "acc", $value = $account['id'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

// New Password
$title = moduleLiteral::get($moduleID, "lbl_newPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password Verify
$title = moduleLiteral::get($moduleID, "lbl_newPasswordVerify");
$input = $form->getInput($type = "password", $name = "password_verify", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


return $page->getReport();
//#section_end#
?>