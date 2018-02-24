<?php
//#section#[header]
// Module Declaration
$moduleID = 201;

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
//#section_end#
//#section#[code]
use \API\Comm\mail\rbMailer;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
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
		$err_header = moduleLiteral::get($moduleID, "lbl_applicantFullname");
		$err = $errFormNtf->addErrorHeader("lbl_name_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_name_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Email
	if (empty($_POST['email']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_applicantEmail");
		$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_email_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Phone
	if (empty($_POST['phone']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_applicantPhone");
		$err = $errFormNtf->addErrorHeader("lbl_subject_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_subject_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create mailer
	$mailer = new rbMailer("contact");
	
	// Add recipients
	$mailer->AddAddress("papikas.ioan@gmail.com");
	$mailer->AddAddress("foudoulisathanasios@outlook.com.gr");
	$mailer->AddAddress("limpakos@hotmail.com");
	
	// Set message
	$message = "<pre>";
	$message .= "<h4>Application Information</h4>";
	$message .= "<u>Name</u>\n".$_POST['fullname']."\n\n";
	$message .= "<u>Email</u>\n".$_POST['email']."\n\n";
	$message .= "<u>Phone</u>\n".$_POST['phone']."\n\n";
	$message .= "<u>Address</u>\n".$_POST['address']."\n\n";
	$message .= "<u>City</u>\n".$_POST['city']."\n\n";
	$message .= "<u>Country</u>\n".$_POST['country']."\n\n";
	$message .= "<u>Details</u>\n".$_POST['details'];
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$subject = "Redback Internship Application Form";
	$from = array();
	$from["contact@redback.gr"] = "Redback Contact";
	$mailer->send($subject, $from);
	ob_clean();
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$message = DOM::create("span", "Your application has been submitted. Thank you for applying to our internship program.");
	$succFormNtf->append($message);
	return $succFormNtf->getReport();
}
//#section_end#
?>