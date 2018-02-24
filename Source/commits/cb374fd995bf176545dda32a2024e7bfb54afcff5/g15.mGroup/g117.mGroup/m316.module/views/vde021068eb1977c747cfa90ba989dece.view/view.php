<?php
//#section#[header]
// Module Declaration
$moduleID = 316;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \SYS\Resources\settings\accSettings;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Server Title
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_accountName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Username
	if (empty($_POST['username']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_accountUsername");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check password
	if (empty($_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_accountPassword");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check dbms
	if (empty($_POST['type']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_accountType");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create account
	$accountName = engine::getVar("name");
	$accountType = engine::getVar("type");
	$accMan = new accSettings($accountType, $accountName);
	$status = $accMan->create();
	
	// If there is an error in creating the module group, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_addAccount");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error adding account..."));
		return $errFormNtf->getReport();
	}
	
	// Update info
	$accMan->updateAccount($_POST['username'], $_POST['password']);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_addAccount");
$frame->build($title, "", FALSE)->engageModule($moduleID, "addAccount");
$form = $frame->getFormFactory();

// Account name
$title = moduleLiteral::get($moduleID, "lbl_accountName");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Account username
$title = moduleLiteral::get($moduleID, "lbl_accountUsername");
$input = $form->getInput($type = "text", $name = "username", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Account password
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

$title = moduleLiteral::get($moduleID, "lbl_accountType");
$typeList = array();
$typeList['ftp'] = "ftp";
$typeList['mail'] = "mail";
$typeList['social'] = "social";
$input = $form->getResourceSelect($name = "type", $multiple = FALSE, $class = "", $typeList, $selectedValue = "");
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>