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
	return $actionFactory->getReportRedirect("/profile/", "", $formSubmit = FALSE);

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
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	if ($_POST['password'] != $_POST['password_verify'])
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "password_verify");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.validate"));
	}
	
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Reset account password
	$status = account::resetPassword($resetID, $_POST['password']);
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_newPassword");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error Updating Password..."));
		return $errFormNtf->getReport();
	}
	
	// Go to login page
	return $actionFactory->getReportRedirect("/login/", "", $formSubmit = TRUE);
}

if (empty($resetID))
	return $actionFactory->getReportRedirect("/login/", "", $formSubmit = TRUE);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($title, "resetPasswordPage", TRUE, TRUE);

// Create form
$formContainer = HTML::select(".resetPassword .whiteBox .formContainer")->item(0);
$form = new simpleForm("resetPassword");
$resetForm = $form->build("", FALSE)->engageModule($moduleID)->get();
DOM::append($formContainer, $resetForm);

// Safety values
$input = $form->getInput($type = "hidden", $name = "rs", $value = $resetID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

$input = $form->getInput($type = "text", $name = "password", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_newPassword", array(), FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
$form->append($input);

$input = $form->getInput($type = "text", $name = "password_verify", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_newPasswordVerify", array(), FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_resetButton");
$sendBtn = $form->getSubmitButton($title, $id = "");
$form->append($sendBtn);


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