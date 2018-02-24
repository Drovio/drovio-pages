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
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();


// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$title = moduleLiteral::get($moduleID, "lbl_projectMemberManager", array(), FALSE);
$page->build($title." | ".$projectTitle, "projectMemberManager", TRUE);


// Get project members
$members = $project->getProjectAccounts();
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
		$attr['id'] = $projectID;
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
	$attr['id'] = $projectID;
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

// Add new member form
$formContainer = HTML::select(".projectMembers .add_new_member .searchFormContainer")->item(0);
$form = new simpleForm("searchForm");
$searchForm = $form->build("", FALSE)->engageModule($moduleID, "searchAccounts")->get();
DOM::append($formContainer, $searchForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
$form->append($input);

$ph = moduleLiteral::get($moduleID, "lbl_accountSearch_ph", array(), FALSE);
$input = $form->getInput($type = "search", $name = "search_q", $value = "", $class = "tminp");
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_search");
$button = $form->getSubmitButton($title, $id = "btn_search");
$form->append($button);


// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>