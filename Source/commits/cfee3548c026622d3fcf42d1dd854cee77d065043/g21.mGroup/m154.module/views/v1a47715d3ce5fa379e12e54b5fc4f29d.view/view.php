<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Html\HTMLContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get credentials
	$accountID = $_POST['accountID'];
	$username = account::getUsername();
	$password = $_POST['accPassword'];
	
	// Validate account
	$valid = account::authenticate($username, $password, $accountID);
	if (!$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global.dictionary", "password");
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
$frame->build("Switch Account", $moduleID, "switchAccount", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_switchAccount_header");
$hdr = DOM::create("h3");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Hidden account id
$input = $sForm->getInput($type = "hidden", $name = "accountID", $value = $_GET['accID'], $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = literal::get("global.dictionary", "password");
$input = $sForm->getInput($type = "password", $name = "accPassword", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>