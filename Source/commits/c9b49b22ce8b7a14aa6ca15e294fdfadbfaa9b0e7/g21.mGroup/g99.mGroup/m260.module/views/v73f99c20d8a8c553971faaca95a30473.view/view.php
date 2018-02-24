<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\team;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Presentation\popups\popup;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

// Get member id for GET and POST
$memberID = $_REQUEST['aid'];
$teamID = team::getTeamID();
$keys = accountKey::get($memberID);
// Get keys
$roles = array();
$akeys = array();
foreach ($keys as $key)
	if ($key['type_id'] == 1 AND $key['context'] == $teamID)
	{
		$roles[$key['userGroup_id']] = $key['groupName'];
		$akeys[$key['userGroup_id']] = $key['akey'];
	}

if (engine::isPost())
{
	// Remove roles not checked
	foreach ($roles as $id => $role)
		if (!isset($_POST['tg'][$id]))
			accountKey::remove($akeys[$id]);
			
	// Add extra roles
	foreach ($_POST['tg'] as $id => $role)
	{
		privileges::addAccountToGroupID($memberID, $id);
		if (!isset($roles[$id]))
			accountKey::create($id, 1, $teamID, $memberID);
	}
	
	$fnt = new formNotification();
	$fnt->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $fnt->getMessage("success", "success.save_success");
	$fnt->append($errorMessage);
	
	// Reload members
	$fnt->addReportAction("members.reload");
	
	return $fnt->getReport(FALSE);
}

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "teamRolesEditor", TRUE);

// Build form
$formContainer = HTML::select(".editorFormContainer")->item(0);
$form = new simpleForm();
$roleEditorForm = $form->build($moduleID, "editRoles", TRUE)->get();
DOM::append($formContainer, $roleEditorForm);

// Add member id
$input = $form->getInput("hidden", "aid", $memberID);
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
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_usergroups");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$teamGroups = $dbc->fetch($result, TRUE);
foreach ($teamGroups as $group)
{
	$row = array();
	$row[] = $group['name'];
	$gridList->insertRow($row, "tg[".$group['id']."]", isset($roles[$group['id']]));
}

// Create popup
$pp = new popup();
$pp->position("bottom|right");
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>