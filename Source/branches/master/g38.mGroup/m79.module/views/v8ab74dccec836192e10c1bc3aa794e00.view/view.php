<?php
//#section#[header]
// Module Declaration
$moduleID = 79;

// Inner Module Codes
$innerModules = array();
$innerModules['frontend'] = 70;

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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\mail\mailer;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
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
	$mailer = new mailer("contact");
	
	// Add recipients
	$mailer->AddAddress("papikas.ioan@gmail.com");
	$mailer->AddAddress("foudoulisathanasios@outlook.com.gr");
	
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
	$from["contact@redback.gr"] = "Redback Contact Form";
	$mailer->send($subject, $from);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = DOM::create("span", "Your message has been sent with success.");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Module Page
$page = new MPage($moduleID);

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "contactPage", TRUE);

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['frontend'], "navigationBar");
DOM::append($navBar, $navigationBar);


// Create contact form
$formContainer = HTML::select(".formContainer")->item(0);
$form = new simpleForm("contact");
$cForm = $form->build($moduleID)->get();
DOM::append($formContainer, $cForm);


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


// Load footer menu
$discoverPage = HTML::select(".contact")->item(0);
$footerMenu = module::loadView($innerModules['frontend'], "footerMenu");
DOM::append($discoverPage, $footerMenu);

return $page->getReport();
//#section_end#
?>