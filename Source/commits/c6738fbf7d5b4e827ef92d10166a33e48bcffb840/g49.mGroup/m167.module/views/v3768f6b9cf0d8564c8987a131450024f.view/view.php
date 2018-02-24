<?php
//#section#[header]
// Module Declaration
$moduleID = 167;

// Inner Module Codes
$innerModules = array();
$innerModules['developerHome'] = 100;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\mail\mailer;
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \API\Security\privileges;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// If user is guest, redirect to login first
if (!account::validate())
{
	$url = url::resolve("login", "/");
	$params = array();
	$params['return_path'] = "enroll.php";
	$params['return_sub'] = "developer";
	$url = url::get($url, $params);
	return $actionFactory->getReportRedirect($url);
}

// Check if user is already member of the DEVELOPER group
if (account::validate() && privileges::accountToGroup("DEVELOPER"))
	return $actionFactory->getReportRedirect("/", "developer");
	
	
// Build Module Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devEnrollPage", TRUE);
	
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Create mail
	$mailer = new mailer("contact");
	
	// Add recipients
	$mailer->AddAddress("papikas.ioan@gmail.com");
	$mailer->AddAddress("foudoulisathanasios@outlook.com.gr");
	$mailer->AddAddress("limpakos@hotmail.com");
	
	// Set message
	$message = "<pre>";
	$message .= "<h4>Beta Tester Information</h4>";
	$message .= "<u>Name</u>\n".$_POST['fullname']."\n\n";
	$message .= "<u>Email</u>\n".$_POST['email']."\n\n";
	$message .= "<h4>More</h4>".$_POST['more'];
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$subject = "Redback Beta Testing Application";
	$from = array();
	$from["contact@redback.gr"] = "Redback Beta Testing Form";
	$mailer->send($subject, $from);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$message = moduleLiteral::get($moduleID, "lbl_applySuccess");
	$succFormNtf->append($message);
	return $succFormNtf->getReport();
}


$agreeForm = HTML::select(".formContainer")->item(0);
$form = new simpleForm("enrollForm");
$enrollForm = $form->build($moduleID, "", FALSE)->get();
DOM::append($agreeForm, $enrollForm);

// Name
$title = moduleLiteral::get($moduleID, "lbl_fullname");
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Email
$title = literal::dictionary("mail");
$input = $form->getInput($type = "text", $name = "email", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// More about you
$title = moduleLiteral::get($moduleID, "lbl_moreAboutYou");
$input = $form->getTextarea($name = "more", $value = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Terms of Use
$title = moduleLiteral::get($moduleID, "lbl_agreeTerms");
$input = $form->getInput($type = "checkbox", $name = "agree", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_apply");
$button = $form->getSubmitButton($title, $id = "btn_apply");
$form->appendControl($button);

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

return $page->getReport();
//#section_end#
?>