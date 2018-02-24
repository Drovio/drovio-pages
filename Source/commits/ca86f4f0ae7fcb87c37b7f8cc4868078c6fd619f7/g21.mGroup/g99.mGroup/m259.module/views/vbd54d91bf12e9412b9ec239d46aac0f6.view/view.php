<?php
//#section#[header]
// Module Declaration
$moduleID = 259;

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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Security\account;
use \API\Security\accountKey;
use \API\Security\privileges;
use \API\Literals\moduleLiteral;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get posted email
	$teamName = $_POST['name'];
	if (empty($teamName))
		$result = FALSE;
	else
	{
		// Create a new team and add this account to it
		$dbc = new dbConnection();
		$dbq = module::getQuery($moduleID, "create_team");
		
		// Set attributes and execute
		$attr = array();
		$attr['name'] = $teamName;
		$attr['aid'] = account::getAccountID();
		$result = $dbc->execute($dbq, $attr);
		$data = $dbc->fetch($result);
		
		// Add account to TEAM_ADMIN and add key for the same group
		privileges::addAccountToGroup(account::getAccountID(), "TEAM_ADMIN");
		accountKey::create(6, 1, $data['id']);
	}
	
	if ($result)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("p", "Team successfully created.");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport();
	}
	else
	{
		$errorNtf = new formNotification();
		$errorNtf->build($type = "error", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		return $errorNtf->getReport();
	}
}

return FALSE;
//#section_end#
?>