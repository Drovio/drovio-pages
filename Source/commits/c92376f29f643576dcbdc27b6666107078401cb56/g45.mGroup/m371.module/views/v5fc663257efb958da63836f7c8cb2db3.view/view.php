<?php
//#section#[header]
// Module Declaration
$moduleID = 371;

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
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
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
	
	// Get credentials to authenticate
	$username = account::getInstance()->getUsername(TRUE);
	$password = $_POST['password'];
	$authenticate = account::getInstance()->authenticate($username, $password);
	if (!$authenticate)
	{
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Get team id
	$teamID = team::getTeamID();
	$dbc = new dbConnection();
	$attr = array();
	$attr['tid'] = team::getTeamID();
	
	// Remove keys
	$q = module::getQuery($moduleID, "delete_team_keys");
	$dbc->execute($q, $attr);
	
	// Remove team
	$q = module::getQuery($moduleID, "delete_team");
	$dbc->execute($q, $attr);
	
	// Reload page
	$content = new MContent();
	$actionFactory = $content->getActionFactory();
	return $actionFactory->getReportRedirect("/", "www", $formSubmit = TRUE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteTeam");
$frame->build($title, "", TRUE)->engageModule($moduleID, "deleteTeam");
$form = $frame->getFormFactory();

// Subtitle
$attr = array();
$attr['tname'] = team::getTeamName();
$notification = moduleLiteral::get($moduleID, "lb_deleteTeam_notification", $attr);
$subtitle = DOM::create("h2", $notification, "", "delete_hd");
$frame->append($subtitle);


// Hidden account id
$input = $form->getInput($type = "hidden", $name = "accountID", $value = $_GET['aid'], $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>