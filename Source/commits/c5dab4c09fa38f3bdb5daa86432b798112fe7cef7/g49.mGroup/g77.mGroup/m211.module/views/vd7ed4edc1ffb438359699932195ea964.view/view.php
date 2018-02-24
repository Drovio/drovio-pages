<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];

// Get project info
$project = new project($projectID);
$projectInfo = $project->info();


// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$title = moduleLiteral::get($moduleID, "lbl_projectMemberManager", array(), FALSE);
$page->build($title." | ".$projectTitle, "projectMemberManager", TRUE);



// Get project members
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_project_members");
$attr = array();
$attr['pid'] = $projectID;
$result = $dbc->execute($q, $attr);
$members = $dbc->fetch($result, TRUE);
$memberList = HTML::select(".projectMemberManager .memberlist")->item(0);
foreach ($members as $member)
{
	// Build a row with privileges
	$pr = DOM::create("div", "", "", "pr");
	DOM::append($memberList, $pr);
	
	$memberName = $member['title'];
	if ($memberName == "Personal Account")
		$memberName = $member['firstname']." ".$member['lastname'];
	$mName = DOM::create("span", $memberName, "", "mn");
	DOM::append($pr, $mName);
	
	if (account::getAccountID() != $member['accountID'])
	{
		// Remove Member
		$title = moduleLiteral::get($moduleID, "lbl_removeMember");
		$remove = DOM::create("span", $title, "", "edit");
		DOM::append($pr, $remove);
		
		// Set edit action
		$attr = array();
		$attr['aid'] = $member['accountID'];
		$attr['pid'] = $projectID;
		$actionFactory->setModuleAction($remove, $moduleID, "removeMember", "", $attr);
	}
	
	// Edit privileges
	$title = moduleLiteral::get($moduleID, "lbl_editRoles");
	$edit = DOM::create("span", $title, "", "edit");
	DOM::append($pr, $edit);
	
	// Set edit action
	$attr = array();
	$attr['aid'] = $member['accountID'];
	$attr['pid'] = $projectID;
	$actionFactory->setModuleAction($edit, $moduleID, "editMembers", "", $attr);
	
	// Get account keys/roles
	$roles = array();
	$keys = accountKey::get($member['accountID']);
	foreach ($keys as $key)
		if ($key['type_id'] == 2 AND $key['context'] == $projectID)
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$mrl = DOM::create("span", $roleContext, "", "mrl");
	DOM::append($pr, $mrl);
}


// Get team userGroups
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_project_usergroups");
$attr = array();
$result = $dbc->execute($q);
$teamGroups = $dbc->toArray($result, "id", "name");

// Add new member form
$formContainer = HTML::select(".projectMembers .add_new_member")->item(0);
$form = new simpleForm("addMemberForm");
$addMemberForm = $form->build($moduleID, "addMember", FALSE)->get();
DOM::append($formContainer, $addMemberForm);

// Add project id
$input = $form->getInput("hidden", "pid", $projectID);
$form->append($input);

$ph = moduleLiteral::get($moduleID, "lbl_personMail_ph", array(), FALSE);
$personMail = $form->getInput($type = "email", $name = "pmail", $value = "", $class = "tminp");
DOM::attr($personMail, "placeholder", $ph);
$form->append($personMail);

$memberRole = $form->getResourceSelect($name = "role", $multiple = FALSE, $class = "tmsl", $teamGroups, $selectedValue = "");
$form->append($memberRole);

$title = moduleLiteral::get($moduleID, "lbl_addMember");
$addMember = $form->getSubmitButton($title, $id = "btn_add_member");
$form->append($addMember);


// Return the report
return $page->getReport();
//#section_end#
?>