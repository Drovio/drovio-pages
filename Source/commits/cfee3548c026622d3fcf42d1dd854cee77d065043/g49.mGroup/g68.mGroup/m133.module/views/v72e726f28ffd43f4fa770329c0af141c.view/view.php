<?php
//#section#[header]
// Module Declaration
$moduleID = 133;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\formCaptcha;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$errFormNtf->build()->get();
	
	if (empty($_POST['appName']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "ph_appName");
		$err = $errFormNtf->addErrorHeader("ph_appName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "ph_appName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Captcha
	if (!formCaptcha::validate(simpleForm::getPostedFormID(), $_POST['appCaptcha']))
	{
		$has_error = TRUE;
		$header = $errFormNtf->addErrorHeader("err", "CAPTCHA ERROR");
		$errFormNtf->addErrorDescription($header, "errDesc", "Wrong Captcha code.", $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new App
	$app = new application();
	$appName = trim($_POST['appName']);
	$appName = str_replace(" ", "_", $appName);
	$result = $app->create($_POST['appName'], $_POST['appScope'], $_POST['appFullName'], "", $_POST['appDesc']);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error creating application..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame("newAppDialog");
$frame->build("Create New Application", $moduleID, "CreateNewApp", FALSE);
$form = new simpleForm();

// Basic Information Container
$bContainer = DOM::create("div", "", "", "basicContainer");
$frame->append($bContainer);

// Application Ico
$appIco = DOM::create("div", "", "", "appIco");
DOM::append($bContainer, $appIco);

// Application Name
$ph = moduleLiteral::get($moduleID, "ph_appName", FALSE);
$input = $form->getInput($type = "text", $name = "appName", $value = "", $class = "appInput", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
DOM::append($bContainer, $input);

// Application Tags
$ph = moduleLiteral::get($moduleID, "ph_appFullName", FALSE);
$input = $form->getInput($type = "text", $name = "appFullName", $value = "", $class = "appInput", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "placeholder", $ph);
DOM::append($bContainer, $input);

// Application Scope
$chHolder = DOM::create("div", "", "", "chHolder");
DOM::append($bContainer, $chHolder);
$input = $form->getInput($type = "radio", $name = "appScope", $value = "private", $class = "appCheck", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "id", "sc_pr");
DOM::attr($input, "checked", "checked");
DOM::append($chHolder, $input);
$text = moduleLiteral::get($moduleID, "lbl_appScopePrivate");
$label = $form->getLabel($text, $for = "sc_pr", $class = "appLabel");
DOM::append($chHolder, $label);

// Application Scope
$chHolder = DOM::create("div", "", "", "chHolder");
DOM::append($bContainer, $chHolder);
$input = $form->getInput($type = "radio", $name = "appScope", $value = "public", $class = "appCheck", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "id", "sc_pub");
DOM::append($chHolder, $input);
$text = moduleLiteral::get($moduleID, "lbl_appScopePublic");
$label = $form->getLabel($text, $for = "sc_pub", $class = "appLabel");
DOM::append($chHolder, $label);

// Application Scope Notes
$text = moduleLiteral::get($moduleID, "lbl_appScopeNotes");
$appNotes = DOM::create("div", $text, "", "appNotes");
DOM::append($bContainer, $appNotes);

// Application Description
$ph = moduleLiteral::get($moduleID, "ph_appDescription", FALSE);
$input = $form->getTextarea($name = "appDesc", $value = "", $class = "appTextarea", $autofocus = FALSE);
DOM::attr($input, "placeholder", $ph);
DOM::append($bContainer, $input);


$captcha = new formCaptcha();
$formID = $frame->getFormID();
$formCaptcha = $captcha->build($formID, "newAppCaptcha")->get();
DOM::append($bContainer, $formCaptcha);

$ph = moduleLiteral::get($moduleID, "ph_appCaptcha", FALSE);
$input = $form->getInput($type = "text", $name = "appCaptcha", $value = "", $class = "appInput captcha", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
DOM::append($bContainer, $input);

// Return the report frame
return $frame->getFrame(TRUE);
//#section_end#
?>