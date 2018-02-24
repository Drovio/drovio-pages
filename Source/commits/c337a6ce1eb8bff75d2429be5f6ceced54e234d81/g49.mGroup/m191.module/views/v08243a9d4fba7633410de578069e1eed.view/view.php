<?php
//#section#[header]
// Module Declaration
$moduleID = 191;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Comm\mail\mailer;
use \ESS\Environment\url;
use \API\Model\sql\dbQuery;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\person;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "contactDeveloperDialog", TRUE);

// Get accountID
$accountID = engine::getVar('aid');

if (engine::isPost())
{
	// Get account information
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "get_account_info");
	$attr = array();
	$attr['id'] = $accountID;
	$result = $dbc->execute($q, $attr);
	$accountInfo = $dbc->fetch($result);
	$accountMail = $accountInfo['mail'];
	
	// Create mail
	$mailer = new mailer("contact");
	
	// Add recipients
	$mailer->AddAddress($accountMail);
	
	// Reply to current account or given email
	$mailer->AddReplyTo($_POST['email'], $_POST['fullname']);
	
	// Normalize subject
	$subject = "Developer Contact";
	$mailer->subject($subject);
 	
	// Set message
	$message = "<pre>";
	$message .= "<h4>Here is a message for you from your developer profile here in Redback:</h4>";
	
	$inviteContext = moduleLiteral::get($moduleID, "lbl_contactContext", array(), FALSE);
	$message .= "<p>".$inviteContext."</p>";
	$message .= "<p>".engine::getVar("message")."</p>";
	
	// Add account email
	$accountInfo = account::info();
	$message .= "<b>".$_POST['fullname']." (".$_POST['email'].")</b>";
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$sender = array();
	$sender["contact@redback.io"] = 'Redback Contact Engine';
	$mailer->send($subject, $sender);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = moduleLiteral::get($moduleID, "lbl_contactSuccess");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Initialize frame
$title = moduleLiteral::get($moduleID, "hd_contactDialog");
$frame = new dialogFrame();
$frame->build($title)->engageModule($moduleID, "contactDialog");
$form = $frame->getFormFactory();

// Append initial content
$frame->append($pageContent->get());

// Account ID to contact
$input = $form->getInput($type = "hidden", $name = "aid", $value = $accountID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

if (account::validate())
{
	// Add account information to the dialog
	$personInfo = person::info();
	
	// Person fullname
	$title = moduleLiteral::get($moduleID, "lbl_fullname");
	$label = $form->getLabel($text = $personInfo['firstname']." ".$personInfo['lastname'], $for = "", $class = "");
	$inputRow = $form->buildRow($title, $label, $required = TRUE, $notes = "");
	$frame->append($inputRow);
	
	$input = $form->getInput($type = "hidden", $name = "fullname", $value = $personInfo['firstname']." ".$personInfo['lastname'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Person email
	$title = moduleLiteral::get($moduleID, "lbl_email");
	$label = $form->getLabel($text = $personInfo['mail'], $for = "", $class = "");
	$inputRow = $form->buildRow($title, $label, $required = TRUE, $notes = "");
	$frame->append($inputRow);
	
	$input = $form->getInput($type = "hidden", $name = "email", $value = $personInfo['mail'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
}
else
{
	// Person fullname
	$title = moduleLiteral::get($moduleID, "lbl_fullname");
	$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$frame->append($inputRow);
	
	// Person email
	$title = moduleLiteral::get($moduleID, "lbl_email");
	$input = $form->getInput($type = "email", $name = "email", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$frame->append($inputRow);
}

// Personal contact message
$title = moduleLiteral::get($moduleID, "lbl_personalMessage");
$input = $form->getTextarea($name = "message", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

return $frame->getFrame();
//#section_end#
?>