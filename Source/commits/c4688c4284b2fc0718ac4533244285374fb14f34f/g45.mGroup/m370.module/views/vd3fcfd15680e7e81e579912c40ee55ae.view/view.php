<?php
//#section#[header]
// Module Declaration
$moduleID = 370;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Comm\mail\mailer;
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

// Get team id
$teamID = engine::getVar('tid');
$teamInfo = team::info($teamID);

if (engine::isPost())
{
	// Get account ids to add to project
	$accountIDs = engine::getVar("accs");
	$teamRole = engine::getVar("role");
	
	$status = FALSE;
	foreach ($accountIDs as $accountInfo => $nothing)
	{
		// Split info
		$acc = explode(":", $accountInfo);
		$accountID = $acc[0];
		$accountMail = $acc[1];
		
		// Create the invitation
		invitations::create($accountMail, $teamID, invitations::TEAM_TYPE);
		
		// Send email notification
		if ($accountID == -1)
		{
			// Send invitation email
			$attr = array();
			$attr['team_name'] = $teamInfo['name'];
			$attr['account_title'] = account::getAccountTitle();
			$attr['email'] = $searchQ;
			$status = mMail::send("/mail/invitations/team_invitation_guest.html", "Team Invitation", $searchQ, $attr);
		}
		else
		{
			$attr = array();
			$attr['team_name'] = $teamInfo['name'];
			$attr['inviter_title'] = account::getAccountTitle();
			$memberInfo = account::info($accountID);
			$attr['member_title'] = $memberInfo['accountTitle'];
			$subject = $attr['inviter_title']." invited you to join ".$attr['team_name']." on Redback";
			$status = mMail::send("/mail/invitations/team_invitation_user.html", $subject, $accountMail, $attr);
		}
	}
	
	if ($status)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("p", "Team members successfully added.");
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