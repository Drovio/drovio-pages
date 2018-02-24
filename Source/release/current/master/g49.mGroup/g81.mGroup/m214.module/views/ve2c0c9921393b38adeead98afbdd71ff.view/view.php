<?php
//#section#[header]
// Module Declaration
$moduleID = 214;

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
importer::import("API", "Literals");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\mail\mailer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if (engine::isPost())
{
	// Send feedback mail
	$mailer = new mailer("support");
	
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
	$from["no-reply@redback.io"] = "Redback Feedback Form";
	$mailer->send("Redback Feedback", $from);
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


$pageContent = new MContent($moduleID);
$pageContent->build("", "feedReporter", TRUE);

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_dialogTitle");
$frame->build($title, $moduleID, "", FALSE);
$form = $frame->getFormFactory();

// Append page content
$frame->append($pageContent->get());

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

// Return the report
return $frame->getFrame();
//#section_end#
?>