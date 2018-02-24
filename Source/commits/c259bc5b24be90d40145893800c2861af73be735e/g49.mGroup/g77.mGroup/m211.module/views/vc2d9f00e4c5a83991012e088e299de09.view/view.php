<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Comm\mail\mailer;
use \API\Model\modules\module;
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
use \API\Security\privileges;
use \API\Literals\moduleLiteral;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;

if (engine::isPost())
{
	// Get project id
	$projectID = engine::getVar("id");
	
	// Get account ids to add to project
	$accountIDs = engine::getVar("accs");
	$projectRole = engine::getVar("role");
	
	$status = FALSE;
	foreach ($accountIDs as $accountID => $nothing)
	{
		// Split info
		$acc = explode(":", $accountInfo);
		$accountID = $acc[0];
		$accountMail = $acc[1];
		
		// Add account to project
		$dbc = new dbConnection();
		$q = module::getQuery($moduleID, "add_account_to_project");
		$attr = array();
		$attr['aid'] = $accountID;
		$attr['pid'] = $projectID;
		$status = $dbc->execute($q, $attr);
		
		// Create account key for the given role
		privileges::addAccountToGroupID($accountID, $projectRole);
		accountKey::create($projectRole, accountKey::PROJECT_KEY_TYPE, $projectID, $accountID);
		
		$project = new project($projectID);
		$projectInfo = $project->info();
		
		// Send email notification
		$attr = array();
		$attr['project_title'] = $projectInfo['title'];
		$attr['inviter_title'] = account::getAccountTitle();
		$memberInfo = account::info($accountID);
		$attr['member_title'] = $memberInfo['accountTitle'];
		$subject = $attr['inviter_title']." invited you to join ".$attr['project_title']." on Redback";
		$status = mMail::send("/resources/mail/invitations/project_invitation_user.html", $subject, $accountMail, $attr);
	}
	
	if ($status)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = FALSE);
		
		// Notification Message
		$errorMessage = DOM::create("h2", "Project members successfully added.");
		$succFormNtf->append($errorMessage);
		
		$succFormNtf->addReportAction("members.reload");
		return $succFormNtf->getReport();
	}
	else
	{
		$errorNtf = new formNotification();
		$errorNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		$title = moduleLiteral::get($moduleID, "lbl_personNotRegistered");
		$hd = DOM::create("p", $title);
		$errorNtf->append($hd);
		return $errorNtf->getReport();
	}
}

return FALSE;
//#section_end#
?>