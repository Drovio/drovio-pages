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
importer::import("API", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
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

$resetID = engine::getVar('rs');
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
		$err = $errFormNtf->addHeader("lbl_newPassword_h", $err_header);
		$errFormNtf->addDescription($err, "lbl_newPassword_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	if ($_POST['password'] != $_POST['password_verify'])
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "password_verify");
		$err = $errFormNtf->addHeader("lbl_newPasswordVerify_h", $err_header);
		$errFormNtf->addDescription($err, "lbl_newPasswordVerify_desc", $errFormNtf->getErrorMessage("err.validate"));
	}
	
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Reset account password
	$status = account::resetPassword($resetID, $_POST['password']);
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_newPassword");
		$err = $errFormNtf->addHeader("lbl_pass_h", $err_header);
		$errFormNtf->addDescription($err, "lbl_pass_desc", DOM::create("span", "Error Updating Password..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$title = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($title, "resetPasswordPage", TRUE, TRUE);

// Create form
$formContainer = HTML::select(".resetPwPage .whiteBox .main")->item(0);
$form = new simpleForm("resetPassword");
$resetForm = $form->build("", TRUE)->engageModule($moduleID)->get();
DOM::append($formContainer, $resetForm);

// Safety values
$input = $form->getInput($type = "hidden", $name = "rs", $value = $resetID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

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