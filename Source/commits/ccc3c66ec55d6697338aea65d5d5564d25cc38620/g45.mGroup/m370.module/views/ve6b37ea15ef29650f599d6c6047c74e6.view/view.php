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
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get current team id
$teamID = team::getTeamID();

// Get whether the account is team admin
$teamAdmin = accountKey::validateGroup($groupName = "TEAM_ADMIN", $context = $teamID, $type = accountKey::TEAM_KEY_TYPE);

$pageContent->build("", "teamMembersViewer", TRUE);
$teamList = HTML::select(".teamMembersViewer .teamList")->item(0);

$dbc = new dbConnection();
// Get team members
$q = module::getQuery($moduleID, "get_team_members");
$attr = array();
$attr['tid'] = $teamID;
$result = $dbc->execute($q, $attr);
$members = $dbc->fetch($result, TRUE);
foreach ($members as $member)
{
	// Build a row with privileges
	$tmember = DOM::create("div", "", "", "tmember");
	DOM::append($teamList, $tmember);
	
	$tico = DOM::create("div", "", "", "tmico");
	DOM::append($tmember, $tico);
	
	$tminfo = DOM::create("div", "", "", "tminfo");
	DOM::append($tmember, $tminfo);
	
	$mName = DOM::create("div", $member['title'], "", "tmname");
	DOM::append($tminfo, $mName);
	
	// Add handles for members if team admin
	if (FALSE)//$teamAdmin)
	{
		if (account::getAccountID() != $member['id'])
		{
			// Remove Member
			$title = moduleLiteral::get($moduleID, "lbl_removeMember");
			$remove = DOM::create("span", $title, "", "edit");
			DOM::append($tm, $remove);
			
			// Set edit action
			$attr = array();
			$attr['aid'] = $member['id'];
			$attr['tid'] = $teamID;
			$actionFactory->setModuleAction($remove, $moduleID, "removeMember", "", $attr);
		}
	
		// Edit privileges
		$title = moduleLiteral::get($moduleID, "lbl_editRoles");
		$edit = DOM::create("span", $title, "", "edit");
		DOM::append($tmember, $edit);
		
		// Set edit action
		$attr = array();
		$attr['aid'] = $member['id'];
		$attr['tid'] = $teamID;
		$actionFactory->setModuleAction($edit, $moduleID, "editRoles", "", $attr);
	}

	// Get account keys/roles
	$keys = accountKey::get($member['id']);
	$teamID = $teamID;
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 AND $key['context'] == $teamID)
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$mrl = DOM::create("div", $roleContext, "", "tmrole");
	DOM::append($tminfo, $mrl);
}


// Add new member to team (for team admins)
if ($teamAdmin)
{
	// Add invitations pending
	$pendingInvitations = invitations::getInvitations($teamID, $type = invitations::TEAM_TYPE);
	$invitationsContainer = HTML::select(".teamInvitations")->item(0);
	foreach ($pendingInvitations as $inviteInfo)
	{
		$refID = "iv_".mt_rand();
		$ivrow = DOM::create("div", "", $refID, "ivrow");
		DOM::append($invitationsContainer, $ivrow);
		
		$ivName = DOM::create("span", $inviteInfo['email'], "", "iv ivn");
		DOM::append($ivrow, $ivName);
		
		// Revoke / Delete action
		$formContainer = DOM::create("div", "", "", "iv action");
		DOM::append($ivrow, $formContainer);
		
		$form = new simpleForm();
		$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "revokeInvitation")->get();
		DOM::append($formContainer, $deleteForm);
		
		// Add hidden inputs
		$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "ref", $value = $refID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$title = moduleLiteral::get($moduleID, "lbl_delete_invitation");
		$button = $form->getSubmitButton($title, $id = "", $name = "");
		HTML::addClass($button, "act_button");
		$form->append($button);
		
		
		// Resend email action
		$formContainer = DOM::create("div", "", "", "iv action");
		DOM::append($ivrow, $formContainer);
		
		$form = new simpleForm();
		$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "resendInvitation")->get();
		DOM::append($formContainer, $deleteForm);
		
		// Add hidden inputs
		$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$title = moduleLiteral::get($moduleID, "lbl_resend_invitation");
		$button = $form->getSubmitButton($title, $id = "", $name = "");
		HTML::addClass($button, "act_button");
		$form->append($button);
	}
	if (count($pendingInvitations) == 0)
		HTML::replace($invitationsContainer, NULL);
	
	// Add new member form
	$formContainer = HTML::select(".teamMembersViewer .add_new_member .searchFormContainer")->item(0);
	$form = new simpleForm("addMemberForm");
	$addMemberForm = $form->build("", FALSE)->engageModule($moduleID, "searchAccounts")->get();
	DOM::append($formContainer, $addMemberForm);
	
	// Add project id
	$input = $form->getInput($type = "hidden", $name = "tid", $value = $teamID, $class = "");
	$form->append($input);
	
	$title = moduleLiteral::get($moduleID, "lbl_search");
	$addMember = $form->getSubmitButton($title, $id = "btn_search");
	$form->append($addMember);
	
	$ph = moduleLiteral::get($moduleID, "lbl_search_ph", array(), FALSE);
	$personMail = $form->getInput($type = "search", $name = "search_q", $value = "", $class = "tminp");
	DOM::attr($personMail, "placeholder", $ph);
	$form->append($personMail);
}
else
{
	$newMemberContainer = HTML::select(".teamMembers .add_new_member")->item(0);
	HTML::replace($newMemberContainer, NULL);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>