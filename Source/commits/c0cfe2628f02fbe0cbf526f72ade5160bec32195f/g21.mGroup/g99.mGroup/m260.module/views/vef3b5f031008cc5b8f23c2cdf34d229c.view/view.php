<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

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
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Comm\mail\mailer;
use \API\Model\modules\module;
use \API\Profile\team;
use \API\Security\accountKey;
use \API\Literals\moduleLiteral;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get posted email
	$personEmail = $_POST['pmail'];
	
	// Get admin account from person mail
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "get_account_bymail");
	
	// Set attributes and execute
	$attr = array();
	$attr['mail'] = $personEmail;
	$result = $dbc->execute($dbq, $attr);
	$status = FALSE;
	if ($dbc->get_num_rows($result) > 0)
	{
		// Get account id
		$account = $dbc->fetch($result);
		$accountID = $account['id'];
		
		// Add account to team
		$q = module::getQuery($moduleID, "add_account_to_team");
		$attr = array();
		$attr['aid'] = $accountID;
		$attr['tid'] = team::getTeamID();
		$status = $dbc->execute($q, $attr);
		
		// Create account key for the given role
		accountKey::create($_POST['role'], 1, team::getTeamID(), $accountID);
	}
	
	if ($status)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("p", "Team member successfully added.");
		$succFormNtf->append($errorMessage);
		
		$succFormNtf->addReportAction("members.reload");
		return $succFormNtf->getReport();
	}
	else
	{
		$errorNtf = new formNotification();
		$errorNtf->build($type = "error", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		$title = moduleLiteral::get($moduleID, "lbl_personNotRegistered");
		$hd = DOM::create("p", $title);
		$errorNtf->append($hd);
		return $errorNtf->getReport();
	}
}

return FALSE;
//#section_end#
?>