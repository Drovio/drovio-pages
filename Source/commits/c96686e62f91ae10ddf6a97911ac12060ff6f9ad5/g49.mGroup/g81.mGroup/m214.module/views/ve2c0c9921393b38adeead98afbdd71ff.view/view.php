<?php
//#section#[header]
// Module Declaration
$moduleID = 214;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\mail\rbMailer;
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Send feedback mail
	$mailer = new rbMailer("support");
	
	// Add recipients
	$mailer->AddAddress("papikas.ioan@gmail.com");
	$mailer->AddAddress("foudoulisathanasios@outlook.com.gr");
	$mailer->AddAddress("limpakos@hotmail.com");
	
	// Set message
	$message = "<pre>";
	$message .= "<h3>Redback Feedback</h3>";
	$message .= "<b>Experience Rate: </b>".moduleLiteral::get($moduleID, "lbl_expRate".$_POST['rate'], array(), FALSE)."\n";
	$message .= "<h4>Feedback</h4>".$_POST['feedback'];
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$from = array();
	$from["no-reply@redback.gr"] = "Redback Feedback Form";
	$mailer->send("Redback Feedback", $from);
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


$pageContent = new HTMLContent();
$pageContent->build("", "feedReporter", TRUE);


$title = moduleLiteral::get($moduleID, "lbl_feedbackTitle");
$header = HTML::select(".feedbackDirections .title")->item(0);
DOM::append($header, $title);

// Set directions
$directions = moduleLiteral::get($moduleID, "lbl_feedbackDirections");
$feedbackDirections = HTML::select(".feedbackDirections")->item(0);
DOM::append($feedbackDirections, $directions);


$title = moduleLiteral::get($moduleID, "lbl_reportTitle");
$header = HTML::select(".reportDirections .title")->item(0);
DOM::append($header, $title);

// Set directions
$directions = moduleLiteral::get($moduleID, "lbl_reportDirections");
$reportDirections = HTML::select(".reportDirections")->item(0);
DOM::append($reportDirections, $directions);


$form = new simpleForm();

// Rate
$title = moduleLiteral::get($moduleID, "lbl_rate");
$rateResource = array();
for ($i=5; $i>0; $i--)
	$rateResource[$i] = moduleLiteral::get($moduleID, "lbl_expRate".$i);
$input = $form->getResourceSelect($name = "rate", $multiple = FALSE, $class = "", $rateResource, $selectedValue = "5");
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$pageContent->append($inputRow);

// Feedback
$input = $form->getTextarea($name = "feedback", $value = "", $class = "feed", $autofocus = FALSE);
$placeholder = moduleLiteral::get($moduleID, "lbl_feedback_placeholder", array(), FALSE);
DOM::attr($input, "placeholder", $placeholder);
$pageContent->append($input);

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_dialogTitle");
$frame->build($title, $moduleID, "", FALSE);

// Return the report
return $frame->append($pageContent->get())->getFrame();
//#section_end#
?>