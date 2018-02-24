<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Content");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\person;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Content\HTMLContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get credentials
	$accountID = $_POST['accountID'];
	$username = person::getUsername();
	$password = $_POST['accPassword'];

	// Validate account
	$valid = account::authenticate($username, $password, $accountID);
	if (!$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addErrorHeader("lbl_accPass_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_accPass_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Logout existing account
	$result = account::switchAccount($accountID, $password);
	
	// If there is an error in switching account, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_switchAccount_header");
		$err = $errFormNtf->addErrorHeader("switchAcc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "switchAcc_desc", DOM::create("span", "Error switching account..."));
		return $errFormNtf->getReport();
	}
	
	// Reload page
	$content = new HTMLContent();
	$actionFactory = $content->getActionFactory();
	return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_switchAccount_header");
$frame->build($title, $moduleID, "switchAccount", FALSE);
$sForm = new simpleForm();

// Subtitle
$dbc = new dbConnection();
$q = new dbQuery("177361907", "profile.account");
$attr = array();
$attr['id'] = $_GET['aid'];
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$subtitle = DOM::create("p", $accountInfo['accountTitle']);
$frame->append($subtitle);


// Hidden account id
$input = $sForm->getInput($type = "hidden", $name = "accountID", $value = $_GET['aid'], $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = literal::dictionary("password");
$input = $sForm->getInput($type = "password", $name = "accPassword", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>