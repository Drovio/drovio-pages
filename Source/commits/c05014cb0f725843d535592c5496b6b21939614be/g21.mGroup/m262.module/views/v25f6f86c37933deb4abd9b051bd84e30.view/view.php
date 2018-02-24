<?php
//#section#[header]
// Module Declaration
$moduleID = 262;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\locale;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \UI\Modules\MPage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// If user is already logged in, go to my settings
if (account::validate())
	return $actionFactory->getReportRedirect("/settings/", "my", $formSubmit = FALSE);
	
$dbc = new dbConnection();

if (engine::isPost())
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
	$q = module::getQuery($moduleID, "get_reset_account");
	$attr = array();
	$attr['reset'] = $_POST['rs'];
	$result = $dbc->execute($q, $attr);
	$account = $dbc->fetch($result);
	
	if ($account['id'] != $_POST['acc'])
		return $errFormNtf->getReport();
	
	// Update account password and erase reset id
	$q = module::getQuery($moduleID, "update_password_and_delete_reset");
	$attr = array();
	$attr['aid'] = $account['id'];
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

$title = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($title, "resetPasswordPage", TRUE, TRUE);

// Validate hash id
$resetID = $_GET['rs'];
	
// Get the account by the reset id 
$q = module::getQuery($moduleID, "get_reset_account");
$attr = array();
$attr['reset'] = $resetID;
$result = $dbc->execute($q, $attr);
$account = $dbc->fetch($result);
if (is_null($account))
{
	$title = moduleLiteral::get($moduleID, "lbl_notValid");
	$header = DOM::create("h3", $title);
	$headerContainer = HTML::select(".resetPwPage .whiteBox .header")->item(0);
	DOM::append($headerContainer, $header);
	return $page->getReport();
}


// Create form
$formContainer = HTML::select(".resetPwPage .whiteBox .main")->item(0);
$form = new simpleForm("resetPassword");
$resetForm = $form->build("", TRUE)->engageModule($moduleID)->get();
DOM::append($formContainer, $resetForm);

// Safety values
$input = $form->getInput($type = "hidden", $name = "rs", $value = $resetID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "acc", $value = $account['id'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

// Account title
$title = moduleLiteral::get($moduleID, "lbl_accountTitle");
$label = $form->getLabel($account['title'], "", "inplbl");
$form->insertRow($title, $label, $required = FALSE, $notes = "");

// New Password
$title = moduleLiteral::get($moduleID, "lbl_newPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// New Password Verify
$title = moduleLiteral::get($moduleID, "lbl_newPasswordVerify");
$input = $form->getInput($type = "password", $name = "password_verify", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Footer year
$trade = HTML::select(".pgFooter .left")->item(0);
$y = DOM::create("span", "".date('Y'));
DOM::append($trade, $y);

// Footer locale
$a_locale = HTML::select("a.locale")->item(0);
$localeInfo = locale::info();
$content = DOM::create("span", $localeInfo['friendlyName']);
DOM::append($a_locale, $content);


return $page->getReport();
//#section_end#
?>