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
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Presentation\popups\popup;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

// Get team and member id
$teamID = engine::getVar('tid');
$memberID = engine::getVar('aid');
$keys = accountKey::get($memberID);

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$dbc = new dbConnection();

// Get keys
$roles = array();
$akeys = array();
foreach ($keys as $key)
	if ($key['team_id'] == $teamID)
	{
		$roles[$key['user_group_id']] = $key['user_group_name'];
		$akeys[$key['user_group_id']] = $key['akey'];
	}

if (engine::isPost())
{
	// Check to remove member
	if (isset($_POST['remove']))
	{
		// Remove member from team and remove any relative keys
		$q = $pageContent->getQuery("remove_team_member");
		$attr = array();
		$attr['aid'] = $memberID;
		$attr['tid'] = $teamID;
		$dbc->execute($q, $attr);
	}
	else
	{
		// Remove roles not checked
		foreach ($roles as $id => $role)
			if (!isset($_POST['tg'][$id]))
				accountKey::remove($akeys[$id], $memberID);
				
		// Add extra roles
		foreach ($_POST['tg'] as $id => $role)
		{
			privileges::addAccountToGroupID($memberID, $id);
			if (!isset($roles[$id]))
				accountKey::create($id, accountKey::TEAM_KEY_TYPE, $teamID, $memberID);
		}
	}
	
	$fnt = new formNotification();
	$fnt->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $fnt->getMessage("success", "success.save_success");
	$fnt->append($errorMessage);
	
	// Reload members
	$fnt->addReportAction("team_members.reload");
	
	return $fnt->getReport(FALSE);
}

// Build the module content
$pageContent->build("", "teamRolesEditor", TRUE);

// Build form
$formContainer = HTML::select(".editorFormContainer")->item(0);
$form = new simpleForm();
$roleEditorForm = $form->build($moduleID, "editRoles", TRUE)->get();
DOM::append($formContainer, $roleEditorForm);

// Add member id
$input = $form->getInput($type = "hidden", $name = "aid", $value = $memberID);
$form->append($input);

// Add team id
$input = $form->getInput($type = "hidden", $name = "tid", $value = $teamID);
$form->append($input);

// Get account team roles
$gridList = new dataGridList();
$roleList = $gridList->build("", TRUE)->get();
$form->append($roleList);

// Set headers
$headers = array();
$headers[] = "Team Role";
$gridList->setHeaders($headers);

// Get team userGroups
$q = $pageContent->getQuery("get_team_usergroups");
$attr = array();
$attr['tid'] = $teamID;
$result = $dbc->execute($q, $attr);
$teamGroups = $dbc->fetch($result, TRUE);
foreach ($teamGroups as $group)
{
	$row = array();
	$row[] = $group['name'];
	$gridList->insertRow($row, "tg[".$group['id']."]", isset($roles[$group['id']]));
}

if ($memberID != account::getAccountID())
{
	// Remove member checkbox
	$title = moduleLiteral::get($moduleID, "lbl_removeMember");
	$input = $form->getInput($type = "checkbox", $name = "remove", $value = "1", $class = "", $autofocus = FALSE, $required = FALSE);
	$frow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$form->append($frow);
}

// Create popup
$pp = new popup();
$pp->position("bottom|center");
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>