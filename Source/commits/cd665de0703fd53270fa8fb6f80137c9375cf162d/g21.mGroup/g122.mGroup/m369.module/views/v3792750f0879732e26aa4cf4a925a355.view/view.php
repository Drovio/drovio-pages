<?php
//#section#[header]
// Module Declaration
$moduleID = 369;

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
importer::import("API", "Connect");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$invContext = engine::getVar('context');
$email = engine::getVar('email');
$type = engine::getVar('type');
$refID = engine::getVar('ref');
$groupID = engine::getVar('rl');
$accountID = account::getAccountID();

if (engine::isPost())
{
	// Accept invitation
	if ($type == invitations::TEAM_TYPE)
	{
		// Add account to team
		$dbc = new dbConnection();
		$q = module::getQuery($moduleID, "add_account_to_team");
		$attr = array();
		$attr['aid'] = $accountID;
		$attr['tid'] = $invContext;
		$status = $dbc->execute($q, $attr);
		
		// Create account key for the given role
		privileges::addAccountToGroupID($accountID, $groupID);
		accountKey::create($groupID, accountKey::TEAM_KEY_TYPE, $invContext, $accountID);
	}
	else
	{
		// Add account to project
		$dbc = new dbConnection();
		$q = module::getQuery($moduleID, "add_account_to_project");
		$attr = array();
		$attr['aid'] = $accountID;
		$attr['pid'] = $invContext;
		$status = $dbc->execute($q, $attr);
		
		// Create account key for the given role
		privileges::addAccountToGroupID($accountID, $groupID);
		accountKey::create($groupID, accountKey::PROJECT_KEY_TYPE, $invContext, $accountID);
	}
	
	// Remove invitation from teh system
	if ($status)
	{
		invitations::remove($email, $invContext, $type);
		
		// Show notification result
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = FALSE, $timeout = TRUE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("h2", "You have accepted the invitation.");
		$succFormNtf->append($errorMessage);
		
		// Add action to delete row
		$succFormNtf->addReportAction($type = "account_invitations.remove", $refID);
		
		return $succFormNtf->getReport($fullReset = TRUE, $holder = "#".$refID);
	}
	else
	{
		// Show notification result
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("p", "An error occurred.");
		$succFormNtf->append($errorMessage);
		
		return $succFormNtf->getReport();
	}
}
//#section_end#
?>