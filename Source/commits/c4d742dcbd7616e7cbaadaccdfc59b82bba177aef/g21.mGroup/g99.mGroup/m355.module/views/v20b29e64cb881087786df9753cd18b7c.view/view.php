<?php
//#section#[header]
// Module Declaration
$moduleID = 355;

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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
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

// Get team information
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');

// Validate team
$validTeam = FALSE;
$teams = team::getAccountTeams();
foreach ($teams as $team)
	if ($team['id'] == $teamID || (!empty($team['uname']) && $team['uname'] == $teamName))
	{
		$teamID = $team['id'];
		$teamName = $team['name'];
		$teamInfo = $team;
		$validTeam = TRUE;
		break;
	}

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
	$tm = DOM::create("div", "", "", "mr");
	DOM::append($teamList, $tm);
	
	$mName = DOM::create("span", $member['title'], "", "mn");
	DOM::append($tm, $mName);
	/*
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
	}*/
	/*
	// Edit privileges
	$title = moduleLiteral::get($moduleID, "lbl_editRoles");
	$edit = DOM::create("span", $title, "", "edit");
	DOM::append($tm, $edit);
	
	// Set edit action
	$attr = array();
	$attr['aid'] = $member['id'];
	$actionFactory->setModuleAction($edit, $moduleID, "editRoles", "", $attr);
	*/
	// Get account keys/roles
	$keys = accountKey::get($member['id']);
	$teamID = $teamID;
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 AND $key['context'] == $teamID)
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$mrl = DOM::create("span", $roleContext, "", "mrl");
	DOM::append($tm, $mrl);
}

/*
// Add new member form
$formContainer = HTML::select(".teamMembersList .add_new_member .searchFormContainer")->item(0);
$form = new simpleForm("addMemberForm");
$addMemberForm = $form->build($moduleID, "searchAccounts", FALSE)->get();
DOM::append($formContainer, $addMemberForm);

$title = moduleLiteral::get($moduleID, "lbl_search");
$addMember = $form->getSubmitButton($title, $id = "btn_search");
$form->append($addMember);

$ph = moduleLiteral::get($moduleID, "lbl_search_ph", array(), FALSE);
$personMail = $form->getInput($type = "search", $name = "search_q", $value = "", $class = "tminp");
DOM::attr($personMail, "placeholder", $ph);
$form->append($personMail);
*/

// Return output
return $pageContent->getReport();
//#section_end#
?>