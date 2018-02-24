<?php
//#section#[header]
// Module Declaration
$moduleID = 165;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

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
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\Url;
use \API\Comm\database\connections\interDbConnection;
use \API\Comm\mail\rbMailer;
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
{
	// Clean buffer
	ob_end_clean();
	ob_start();
	
	// Static redirect
	$url = url::resolve("my", "/settings/");
	header("Location: ".$url);
	
	// Async redirect
	return $actionFactory->getReportRedirect("/settings/", "my", $formSubmit = FALSE);
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$dbc = new interDbConnection();
	
	// Search if the email is valid
	$q = new dbQuery("1206709991", "profile.person");
	$attr = array();
	$attr['mail'] = $_POST['regEmail'];
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
	$attr['mail'] = $_POST['regEmail'];
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
		$sender[0] = 'no-reply@redback.gr';
		$sender[1] = 'Redback No-Reply';

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

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE); 
$page->build($pageTitle, "resetPassword");

// Forgot Password Container
$forgotContainer = DOM::create("div", "", "forgotContainer", "innerContainer");
DOM::append($globalContainer, $forgotContainer);

$pageHeaderContent = moduleLiteral::get($moduleID, "title");
$pageHeader = DOM::create("h2", $pageHeaderContent);
$page->appendToSection("mainContent", $pageHeader);

$resetDirections = moduleLiteral::get($moduleID, "resetDirections");
$resetP = DOM::create("p");
DOM::append($resetP, $resetDirections);
$page->appendToSection("mainContent", $resetP);

// Create form
$form = new simpleForm("resetPassword");
$resetForm = $form->build($moduleID, "", FALSE)->get();
$page->appendToSection("mainContent", $resetForm);

$title = moduleLiteral::get($moduleID, "lbl_email");
$input = $form->getInput($type = "email", $name = "regEmail", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$title = moduleLiteral::get($moduleID, "lbl_sendEmail");
$sendBtn = $form->getSubmitButton($title, $id = "");
DOM::append($formRow, $sendBtn);
$form->append($formRow);


return $page->getReport();
//#section_end#
?>