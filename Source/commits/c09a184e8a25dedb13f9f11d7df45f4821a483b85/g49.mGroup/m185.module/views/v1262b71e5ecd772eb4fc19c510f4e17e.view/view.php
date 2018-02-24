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
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\akeys\apiKey;
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
	$username = account::getInstance()->getUsername(TRUE);
	if (!account::getInstance()->authenticate($username, $_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addHeader("Authentication error");
		$errFormNtf->addDescription($err, "Password does not match the current account.");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	// Create team on identity
	$teamName = trim($_POST['name']);
	$teamUName = str_replace(" ", "_", strtolower($teamName));
	$teamID = team::getTeamInstance()->create(strtolower($teamUName), $teamName);

	// If there is an error in creating team, show it
	if (!$teamID)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_createTeam");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating team..."));
		return $errFormNtf->getReport();
	}

	// Add account to TEAM_ADMIN and add key for the same group
	privileges::addAccountToGroup(account::getInstance()->getAccountID(), "TEAM_ADMIN");
	apiKey::create($typeID = 1, account::getInstance()->getAccountID(), $teamID, $projectID = NULL);
	
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