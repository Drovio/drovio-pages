<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

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
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check team name
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_teamName_placeholder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Authenticate user
	$username = account::getUsername(TRUE);
	if (!account::authenticate($username, $_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addHeader("Authentication error");
		$errFormNtf->addDescription($err, "Password does not match the current account.");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	// Create a new team and add this account to it
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "create_team");
	
	// Set attributes and execute
	$attr = array();
	$attr['name'] = $_POST['name'];
	$attr['aid'] = account::getAccountID();
	$result = $dbc->execute($dbq, $attr);
	$data = $dbc->fetch($result);
	$teamID = $data['id'];
	
	// If there is an error in creating team, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_newTeam");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating team..."));
		return $errFormNtf->getReport();
	}
	
	// Add account to TEAM_ADMIN and add key for the same group
	privileges::addAccountToGroup(account::getAccountID(), "TEAM_ADMIN");
	accountKey::create(6, accountKey::TEAM_KEY_TYPE, $teamID);
	
	// Set active team
	team::switchTeam($teamID, $_POST['password']);
	
	// Create module content
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Reload dashboard page
	return $actionFactory->getReportReload($formSubmit = TRUE);
}
//#section_end#
?>