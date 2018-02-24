<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\person;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize dbConnection
	$dbc = new dbConnection();
	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	
	// Check if username is unique
	$q = module::getQuery($moduleID, "check_unique_username");
	$attr = array();
	$attr['username'] = $_POST['username'];
	$result = $dbc->execute($q, $attr);
	$row = $dbc->fetch($result);
	$count = $row['count'];
	if ($count > 0 && $_POST['username'] != account::getUsername())
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("username");
		$err = $errFormNtf->addErrorHeader("username_h", $err_header);
		$errFormNtf->addErrorDescription($err, "username_desc", $errFormNtf->getErrorMessage("err.exists"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Update Username
	$q = module::getQuery($moduleID, "update_username");
	$attr = array();
	$attr['username'] = $_POST['username'];
	$attr['pid'] = account::getPersonID();
	$attr['aid'] = account::getAccountID();
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in updating the username, show it
	if (!$result)
	{
		$err_header = literal::dictionary("username");
		$err = $errFormNtf->addErrorHeader("username_h", $err_header);
		$errFormNtf->addErrorDescription($err, "username_desc", DOM::create("span", "Error updating username..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Build the content
$pageContent = new MContent($moduleID);
$pageContent->build("", "myUsernameManager", TRUE);

// Get person's information
$person = person::info();

$formContainer = HTML::select(".usernameEditor")->item(0);
$form = new simpleForm();
$personalDataForm = $form->build($moduleID, "usernameManager")->get();
DOM::append($formContainer, $personalDataForm);

// Username
$title = literal::dictionary("username");
$input = $form->getInput($type = "text", $name = "username", $value = $person['username'], $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport();
//#section_end#
?>