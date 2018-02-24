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
importer::import("DEV", "Projects");
importer::import("DEV", "Tools");
importer::import("DRVC", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;
use \DEV\Tools\validator;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "invitationDialogContainer", TRUE);

// Get project id
$projectID = engine::getVar('id');
$project = new project($projectID);
$projectInfo = $project->info();

if (engine::isPost())
{
	$invitationMail = engine::getVar('inv_mail');
	
	// Get drovio account
	$drovioAccount = account::getInstance()->getAccountByUsername($invitationMail, $includeEmail = TRUE, $fullList = FALSE);
	if (!empty($drovioAccount))
	{
		// Search accounts that are not included in this team
		$dbc = new dbConnection();
		$q = $pageContent->getQuery("get_project_members");
		$attr = array();
		$attr['pid'] = $projectID;
		$result = $dbc->execute($q, $attr);
		$members = $dbc->fetch($result, TRUE);
		$account_inProject = array();
		foreach ($members as $memberInfo)
			if ($memberInfo['account_id'] == $drovioAccount['id'])
				$account_inProject = $drovioAccount;
		if (!empty($account_inProject))
		{
			$fnt = new formNotification();
			$fnt->build($type = formNotification::WARNING, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
			
			// Notification Message
			$title = moduleLiteral::get($moduleID, "lbl_account_inProject");
			$hd = DOM::create("h2", $title, "", "hd");
			$fnt->append($hd);
			
			return $fnt->getReport();
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
		invitations::create($invitationMail, $projectID, invitations::PROJECT_TYPE, $_POST['group_id']);
		
		// Send invitation email
		if ($sendEmail)
		{
			$attr = array();
			$attr['project_title'] = $projectInfo['title'];
			$attr['account_title'] = account::getInstance()->getAccountTitle();
			$attr['email'] = $invitationMail;
			$subject = $attr['account_title']." invited you to join ".$attr['project_title']." on Drovio";
			$status = mMail::send("/mail/invitations/project_invitation.html", $subject, $invitationMail, $attr);
		}
		
		// Show notification
		$fnt = new formNotification();
		$fnt->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$title = moduleLiteral::get($moduleID, "lbl_memberInvited");
		$hd = DOM::create("h2", $title, "", "hd");
		$fnt->append($hd);
		
		// Reload invitations
		$fnt->addReportAction("project_invitations.reload");
		
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
		
		return $fnt->getReport();
	}
}


// Add new member form
$formContainer = HTML::select(".invitationDialog .formContainer")->item(0);
$form = new simpleForm();
$addMemberForm = $form->build("", FALSE)->engageModule($moduleID, "invitationDialog")->get();
DOM::append($formContainer, $addMemberForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
$form->append($input);

$ph = moduleLiteral::get($moduleID, "lbl_email_ph", array(), FALSE);
$personMail = $form->getInput($type = "text", $name = "inv_mail", $value = "", $class = "tminp", $autofocus = TRUE, $required = TRUE);
DOM::attr($personMail, "placeholder", $ph);
$form->append($personMail);

// Group id
$dbc = new dbConnection();
$q = $pageContent->getQuery("get_project_usergroups");
$result = $dbc->execute($q);
$projectGroupsResource = $dbc->toArray($result, "id", "name");

$projectGroup = $form->getResourceSelect($name = "group_id", $multiple = FALSE, $class = "tminp", $resource = $projectGroupsResource, $selectedValue = "");
$form->append($projectGroup);

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