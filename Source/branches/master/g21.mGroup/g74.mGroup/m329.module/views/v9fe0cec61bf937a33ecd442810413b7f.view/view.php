<?php
//#section#[header]
// Module Declaration
$moduleID = 329;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get new username
	$username = account::getInstance()->getUsername();
	$newUsername = engine::getVar('username');
	$status = TRUE;
	if ($newUsername != $username)
		$status = account::getInstance()->updateUsername($newUsername);
	
	// If there is an error in updating the username, show it
	if (!$status)
	{
		$err_header = literal::dictionary("username");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating username..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Build the content
$pageContent = new MContent($moduleID);
$pageContent->build("", "myUsernameManager", TRUE);

$formContainer = HTML::select(".usernameEditor")->item(0);
$form = new simpleForm();
$personalDataForm = $form->build("")->engageModule($moduleID, "usernameManager")->get();
DOM::append($formContainer, $personalDataForm);

// Username
$username = account::getInstance()->getUsername();
$title = literal::dictionary("username");
$input = $form->getInput($type = "text", $name = "username", $value = $username, $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport();
//#section_end#
?>