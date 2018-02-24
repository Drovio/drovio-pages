<?php
//#section#[header]
// Module Declaration
$moduleID = 165;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \API\Geoloc\locale;
use \API\Model\sql\dbQuery;
use \API\Model\modules\module;
use \API\Model\modules\mMail;
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
	return $actionFactory->getReportRedirect("/profile/", "www");

if (engine::isPost())
{
	$dbc = new dbConnection();
	
	// Get account/person info by mail
	$q = module::getQuery($moduleID, "account_info");
	$attr = array();
	$attr['mail'] = $_POST['mail'];
	$result = $dbc->execute($q, $attr);
	$accountInfo = $dbc->fetch($result);
	
	if (is_null($accountInfo))
	{
		// Email is not valid
		$errFormNtf = new formErrorNotification();
		$errFormNtf->build();
		
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, moduleLiteral::get($moduleID, "lbl_invalidEmail"));
		
		return $errFormNtf->getReport();
	}
	
	// Reset value
	$passwordResetHash = md5("reset_".$accountInfo['id']."_".$accountInfo['mail']."_".time()."_".mt_rand());
	
	// Update reset password field
	$q = module::getQuery($moduleID, "set_reset_account");
	$attr = array();
	$attr['mail'] = $_POST['mail'];
	$attr['reset'] = $passwordResetHash;
	$result = $dbc->execute($q, $attr);
	
	if ($result)
	{
		// Send email
		$attr = array();
		$attr['member_title'] = $accountInfo['title'];
		$attr['email_address'] = $_POST['mail'];
		$attr['reset_url'] = url::resolve("www", "/profile/reset_pw.php?rs=".$passwordResetHash);
		$attr['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$subject = "Redback password changed";
		mMail::send("/mail/password_reset.html", $subject, $_POST['mail'], $attr);
		
		// Show notifications
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = moduleLiteral::get($moduleID, "lbl_successMessage");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport();
	}
	
	// Create Notification
	$errFormNtf = new formErrorNotification();
	$errFormNtf->build();
	
	$err_header = moduleLiteral::get($moduleID, "lbl_email");
	$err = $errFormNtf->addHeader($err_header);
	$errFormNtf->addDescription($err, "Error processing your request. Please try again later.");
	
	return $errFormNtf->getReport();
}

$title = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($title, "forgotPasswordPage", TRUE, TRUE);

// Get login box
$resetBoxMain = HTML::select(".whiteBox .formContainer")->item(0);

// Create form
$form = new simpleForm("resetPassword");
$resetForm = $form->build($moduleID, "", FALSE)->get();
DOM::append($resetBoxMain, $resetForm);

$input = $form->getInput($type = "text", $name = "mail", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_emailInfo", array(), FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_sendEmail");
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