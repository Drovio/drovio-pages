<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\person;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;

if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get credentials
	$accountID = $_POST['accountID'];
	$password = $_POST['accPassword'];

	// Logout existing account
	$result = account::switchAccount($accountID, $password);
	
	// If there is an error in switching account, show it
	if (!$result)
	{
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Reload page
	$content = new MContent();
	$actionFactory = $content->getActionFactory();
	return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_switchAccount_header");
$frame->build($title, "", TRUE)->engageModule($moduleID, "switchAccount");
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