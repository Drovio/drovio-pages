<?php
//#section#[header]
// Module Declaration
$moduleID = 79;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\mail\rbMailer;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\special\formCaptcha;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Captcha
	if (!formCaptcha::validate(simpleForm::getPostedFormID(), $_POST['contactCaptcha']))
	{
		$has_error = TRUE;
		$header = $errFormNtf->addErrorHeader("err", "CAPTCHA ERROR");
		$errFormNtf->addErrorDescription($header, "errDesc", "Wrong Captcha code.", $extra = "");
	}
	
	// User Info
	// Check Fullname
	if (empty($_POST['fullname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_name");
		$err = $errFormNtf->addErrorHeader("lbl_name_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_name_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Email
	if (empty($_POST['email']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_email_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Subject
	if (empty($_POST['subject']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_subject");
		$err = $errFormNtf->addErrorHeader("lbl_subject_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_subject_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Message
	if (empty($_POST['message']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_message");
		$err = $errFormNtf->addErrorHeader("lbl_message_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_message_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create mailer
	$mailer = new rbMailer("support");
	
	// Add recipients
	$mailer->AddAddress("papikas.ioan@gmail.com");
	$mailer->AddAddress("foudoulisathanasios@outlook.com.gr");
	$mailer->AddAddress("limpakos@hotmail.com");
	
	// Set message
	$message = "<pre>";
	$message .= "<h4>Contact Information</h4>";
	$message .= "<u>Name</u>\n".$_POST['fullname']."\n\n";
	$message .= "<u>Email</u>\n".$_POST['email']."\n\n";
	$message .= "<u>Subject</u>\n".$_POST['subject']."\n\n";
	$message .= "<h4>Message</h4>".$_POST['message'];
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$subject = "Redback Contact Form : ".$_POST['subject'];
	$from = array();
	$from[] = "contact@redback.gr";
	$from[] = "Redback Contact Form";
	$mailer->send($subject, $from);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = DOM::create("span", "Your message has been sent with success.");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "contactPage");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageHeader");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);

$title = moduleLiteral::get($moduleID, "lbl_contactPrompt");
$header = DOM::create("h3", $title);
$page->appendToSection("mainContent", $header);

// Create contact form
$form = new simpleForm("contact");
$cForm = $form->build($moduleID)->get();
$page->appendToSection("mainContent", $cForm);


// Name
$title = moduleLiteral::get($moduleID, "lbl_name");
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$form->insertRow($title, $input, TRUE);

// Email
$title = moduleLiteral::get($moduleID, "lbl_email");
$input = $form->getInput($type = "email", $name = "email", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, TRUE);

// Subject
$title = moduleLiteral::get($moduleID, "lbl_subject");
$optResource = array();
$rsrcSelected = moduleLiteral::get($moduleID, "lbl_subjSupport", array(), FALSE);
$optResource[$rsrcSelected] = $rsrcSelected;
$rsrc = moduleLiteral::get($moduleID, "lbl_subjFeedback", array(), FALSE);
$optResource[$rsrc] = $rsrc;
$rsrc = moduleLiteral::get($moduleID, "lbl_subjCollaboration", array(), FALSE);
$optResource[$rsrc] = $rsrc;
$rsrc = moduleLiteral::get($moduleID, "lbl_subjProblem", array(), FALSE);
$optResource[$rsrc] = $rsrc;
$rsrc = moduleLiteral::get($moduleID, "lbl_subjServices", array(), FALSE);
$optResource[$rsrc] = $rsrc;
$input = $form->getResourceSelect($name = "subject", $multiple = FALSE, $class = "", $optResource, $selectedValue = $rsrcSelected);
$form->insertRow($title, $input, TRUE);

// Message
$title = moduleLiteral::get($moduleID, "lbl_comments");
$input = $form->getTextarea($name = "message", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, TRUE);

$captcha = new formCaptcha();
$captchaElement = $captcha->build("contact")->get();
$form->append($captchaElement);

$input = $form->getInput($type = "text", $name = "contactCaptcha", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow("Captcha Code", $input, $required = TRUE, $notes = "");



return $page->getReport();
//#section_end#
?>