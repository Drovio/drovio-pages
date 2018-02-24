<?php
//#section#[header]
// Module Declaration
$moduleID = 351;

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
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;

$accountID = account::getAccountID();
$teamID = engine::getVar('tid');
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Authenticate account
	$username = account::getUsername($fallback = TRUE);
	$password = $_POST['password'];
	if (!account::authenticate($username, $password, $accountID))
	{
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Remove account from team
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "remove_account_from_team");
	$attr = array();
	$attr['aid'] = $accountID;
	$attr['tid'] = $teamID;
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in switching account, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_leaveTeam_header");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error leaving team..."));
		return $errFormNtf->getReport();
	}
	
	// Remove key access
	$allKeys = accountKey::get($accountID = NULL);
	foreach ($allKeys as $keyInfo)
		if ($keyInfo['context'] == $teamID)
			accountKey::remove($keyInfo['akey']);
	
	// Reload page
	$content = new MContent();
	$actionFactory = $content->getActionFactory();
	return $actionFactory->getReportRedirect("/relations/", "my", $formSubmit = TRUE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_leaveTeam_header");
$frame->build($title)->engageModule($moduleID, "leaveTeam");
$sForm = $frame->getFormFactory();

// Subtitle
$teams = team::getAccountTeams();
foreach ($teams as $team)
	if ($team['id'] == $teamID)
		$teamName = $team['name'];


$attr = array();
$attr['name'] = $teamName;
$title = moduleLiteral::get($moduleID, "lbl_leaveTeam", $attr);
$subtitle = DOM::create("p", $title);
$frame->append($subtitle);

// Hidden account id
$input = $sForm->getInput($type = "hidden", $name = "tid", $value = $teamID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $sForm->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>