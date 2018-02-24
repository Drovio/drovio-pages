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
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "teamMembersList", TRUE);
$teamList = HTML::select(".teamMembersList .teamList")->item(0);

$dbc = new dbConnection();
// Get team members
$q = module::getQuery($moduleID, "get_team_members");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$members = $dbc->fetch($result, TRUE);
foreach ($members as $member)
{
	// Build a row with privileges
	$tm = DOM::create("div", "", "", "mr");
	DOM::append($teamList, $tm);
	
	$mName = DOM::create("span", $member['title'], "", "mn");
	DOM::append($tm, $mName);
	
	if (account::getAccountID() != $member['id'])
	{
		// Remove Member
		$title = moduleLiteral::get($moduleID, "lbl_removeMember");
		$remove = DOM::create("span", $title, "", "edit");
		DOM::append($tm, $remove);
		
		// Set edit action
		$attr = array();
		$attr['aid'] = $member['id'];
		$actionFactory->setModuleAction($remove, $moduleID, "removeMember", "", $attr);
	}
	
	// Edit privileges
	$title = moduleLiteral::get($moduleID, "lbl_editRoles");
	$edit = DOM::create("span", $title, "", "edit");
	DOM::append($tm, $edit);
	
	// Set edit action
	$attr = array();
	$attr['aid'] = $member['id'];
	$actionFactory->setModuleAction($edit, $moduleID, "editRoles", "", $attr);
	
	// Get account keys/roles
	$keys = accountKey::get($member['id']);
	$teamID = team::getTeamID();
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 AND $key['context'] == $teamID)
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$mrl = DOM::create("span", $roleContext, "", "mrl");
	DOM::append($tm, $mrl);
}


// Get team userGroups
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_usergroups");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$teamGroups = $dbc->toArray($result, "id", "name");

// Add new member form
$formContainer = HTML::select(".teamMembersList .add_new_member")->item(0);
$form = new simpleForm("addMemberForm");
$addMemberForm = $form->build($moduleID, "addMember", FALSE)->get();
DOM::append($formContainer, $addMemberForm);

$title = moduleLiteral::get($moduleID, "lbl_addMember");
$addMember = $form->getSubmitButton($title, $id = "btn_add_member");
$form->append($addMember);

$memberRole = $form->getResourceSelect($name = "role", $multiple = FALSE, $class = "tmsl", $teamGroups, $selectedValue = "");
$form->append($memberRole);

$ph = moduleLiteral::get($moduleID, "lbl_personMail_ph", array(), FALSE);
$personMail = $form->getInput($type = "email", $name = "pmail", $value = "", $class = "tminp");
DOM::attr($personMail, "placeholder", $ph);
$form->append($personMail);

// Return output
return $pageContent->getReport();
//#section_end#
?>