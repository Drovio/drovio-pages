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
importer::import("API", "Connect");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("DEV", "Tools");
importer::import("DRVC", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\privileges;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;
use \DEV\Tools\validator;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "invitationDialogContainer", TRUE);

// Get team id
$teamID = engine::getVar('tid');
$teamInfo = team::info($teamID);

if (engine::isPost())
{
	$invitationMail = engine::getVar('inv_mail');
	
	// Search all accounts
	$drovioAccount = account::getInstance()->getAccountByUsername($invitationMail, $includeEmail = TRUE, $fullList = FALSE);
	if (!empty($drovioAccount))
	{
		// Search accounts that are not included in this team
		$members = team::getTeamMembers();
		$account_inTeam = array();
		foreach ($members as $memberInfo)
			if ($memberInfo['id'] == $drovioAccount['id'])
				$account_inTeam = $drovioAccount;
		if (!empty($account_inTeam))
		{
			$fnt = new formNotification();
			$fnt->build($type = formNotification::WARNING, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
			
			// Notification Message
			$title = moduleLiteral::get($moduleID, "lbl_account_inTeam");
			$hd = DOM::create("h2", $title, "", "hd");
			$fnt->append($hd);
			
			return $fnt->getReport($reset = FALSE);
		}
		
		$invitationMail = $drovioAccount['mail'];
	}
	else if (!validator::validEmail($invitationMail))
		$invitationMail = NULL;

	if (!empty($invitationMail))
	{
		// Get invitation Mail
		$sendEmail = TRUE;
		if (!empty($drovioAccount) && !$drovioAccount['administrator'])
		{
			// Get dummy email
			$invitationMail = invitations::getManagedEmail($drovioAccount['username']);
			
			// Do not send email
			$sendEmail = FALSE;
		}
		
		// Create the invitation
		invitations::create($invitationMail, $teamID, invitations::TEAM_TYPE, $_POST['group_id']);
		
		// Send invitation email
		if ($sendEmail)
		{
			$attr = array();
			$attr['team_name'] = $teamInfo['name'];
			$attr['account_title'] = account::getInstance()->getAccountTitle();
			$attr['email'] = $invitationMail;
			$subject = $attr['account_title']." invited you to join ".$attr['team_name']." on Drovio";
			$status = mMail::send("/mail/invitations/team_invitation.html", $subject, $invitationMail, $attr);
		}
		
		// Show notification
		$fnt = new formNotification();
		$fnt->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$title = moduleLiteral::get($moduleID, "lbl_memberInvited");
		$hd = DOM::create("h2", $title, "", "hd");
		$fnt->append($hd);
		
		// Reload invitations
		$fnt->addReportAction("team_invitations.reload");
		
		return $fnt->getReport();
	}
	else
	{
		$fnt = new formNotification();
		$fnt->build($type = formNotification::WARNING, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$title = moduleLiteral::get($moduleID, "lbl_notValid");
		$hd = DOM::create("h2", $title, "", "hd");
		$fnt->append($hd);
		
		return $fnt->getReport($reset = FALSE);
	}
}


// Add new member form
$formContainer = HTML::select(".invitationDialog .formContainer")->item(0);
$form = new simpleForm();
$addMemberForm = $form->build("", FALSE)->engageModule($moduleID, "invitationDialog")->get();
DOM::append($formContainer, $addMemberForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "tid", $value = $teamID, $class = "");
$form->append($input);

$ph = moduleLiteral::get($moduleID, "lbl_email_ph", array(), FALSE);
$personMail = $form->getInput($type = "text", $name = "inv_mail", $value = "", $class = "tminp", $autofocus = TRUE, $required = TRUE);
DOM::attr($personMail, "placeholder", $ph);
$form->append($personMail);


// Group id
$teamGroupsResource = array();
$teamGroups = privileges::getPermissionGroups();
foreach ($teamGroups as $groupID => $groupName)
	if (preg_match('/TEAM_.*/', $groupName))
		$teamGroupsResource[$groupID] = $groupName;
$teamGroup = $form->getResourceSelect($name = "group_id", $multiple = FALSE, $class = "tminp", $resource = $teamGroupsResource, $selectedValue = "");
$form->append($teamGroup);


$title = moduleLiteral::get($moduleID, "lbl_invite");
$addMember = $form->getSubmitButton($title, $id = "btn_invite");
$form->append($addMember);


// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>