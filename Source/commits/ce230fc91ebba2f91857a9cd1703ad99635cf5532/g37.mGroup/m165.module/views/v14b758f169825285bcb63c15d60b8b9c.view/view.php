<?php
//#section#[header]
// Module Declaration
$moduleID = 165;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Comm\database\connections\interDbConnection;
use \API\Comm\mail\rbMailer;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Modules\MPage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// If user is already logged in, go to my settings
if (account::validate())
	return $actionFactory->getReportRedirect(url::resolve("my", "/settings/"));

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$dbc = new interDbConnection();
	
	// Search if the email is valid
	$q = new dbQuery("1206709991", "profile.person");
	$attr = array();
	$attr['mail'] = $_POST['mail'];
	$result = $dbc->execute($q, $attr);
	$person = $dbc->fetch($result);
	
	if (is_null($person))
	{
		// Email is not valid
		$errFormNtf = new formErrorNotification();
		$errFormNtf->build();
		
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
		$content = moduleLiteral::get($moduleID, "lbl_invalidEmail");
		$errFormNtf->addErrorDescription($err, "lbl_email_desc", $content);
		
		return $errFormNtf->getReport();
	}
	
	// Reset value
	$passwordResetHash = md5($person['id']."|".$person['mail']."|".time());
	// Update reset password field
	$q = new dbQuery("318436259", "profile.account");
	$attr = array();
	$attr['mail'] = $_POST['mail'];
	$attr['reset'] = $passwordResetHash;
	$result = $dbc->execute($q, $attr);
	
	if ($result)
	{
		// Create mail
		$mailer = new rbMailer("support");
		
		// Normalize subject
		$subject = "Redback Reset Password";
		$mailer->setSubject($subject);
	 	
		// Set message
		$message = "<pre>";
		$message .= "<h4>Redback Password Reset</h4>";
		$message .= "<p>You requested to reset your password.<p>";
	
		$url = Url::resolve("my", "/resetPassword.php?rs=".$passwordResetHash, FALSE, TRUE);
		$message .= "<a href=\"".$url."\" target=\"_blank\">Click Here to Reset Your Password</a>";
		$message .= "<p>If this is not your request, please ignore this message or login and change your password in order to secure your account.<p>";
		$message .= "</pre>";
		$mailer->MsgHTML($message);
		
		// Send message 		
		$sender = array();
		$sender["no-reply@redback.gr"] = 'Redback Security';

		$personEmail = $person['mail'];
		$mailer->send($subject, $sender, $personEmail);
		
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
		
		// Notification Message
		$errorMessage = moduleLiteral::get($moduleID, "lbl_successMessage");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport();
	}
	
	// Create Notification
	$errFormNtf = new formErrorNotification();
	$errFormNtf->build();
	
	$err_header = moduleLiteral::get($moduleID, "lbl_email");
	$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
	$errFormNtf->addErrorDescription($err, "lbl_email_desc", "Error processing your request. Please try again later.");
	
	return $errFormNtf->getReport();
}

$title = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($title, "resetPwPageContainer", TRUE);

// Get login box
$resetBoxMain = HTML::select(".whiteBox .main")->item(0);

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