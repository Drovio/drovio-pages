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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Presentation\popups\popup;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

// Get project and member
$projectID = engine::getVar('id');
$memberID = engine::getVar("aid");

// Get keys
$keys = accountKey::get($memberID);
$roles = array();
$akeys = array();
foreach ($keys as $key)
	if ($key['type_id'] == 2 AND $key['context'] == $projectID)
	{
		$roles[$key['userGroup_id']] = $key['groupName'];
		$akeys[$key['userGroup_id']] = $key['akey'];
	}

if (engine::isPost())
{
	// Remove roles not checked
	foreach ($roles as $id => $role)
		if (!isset($_POST['pg'][$id]))
			accountKey::remove($akeys[$id]);
			
	// Add extra roles
	foreach ($_POST['pg'] as $id => $role)
	{
		privileges::addAccountToGroupID($memberID, $id);
		if (!isset($roles[$id]))
			accountKey::create($id, 2, $projectID, $memberID);
	}
	
	$fnt = new formNotification();
	$fnt->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $fnt->getMessage("success", "success.save_success");
	$fnt->append($errorMessage);
	// Report action
	$fnt->addReportAction("members.reload");
	
	return $fnt->getReport(FALSE);
}

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "projectRolesEditor", TRUE);

// Build form
$formContainer = HTML::select(".editorFormContainer")->item(0);
$form = new simpleForm();
$roleEditorForm = $form->build($moduleID, "editMembers", TRUE)->get();
DOM::append($formContainer, $roleEditorForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
$form->append($input);

// Add member id
$input = $form->getInput($type = "hidden", $name = "aid", $value = $memberID, $class = "");
$form->append($input);

// Get account project roles
$gridList = new dataGridList();
$roleList = $gridList->build("", TRUE)->get();
$form->append($roleList);

// Set headers
$headers = array();
$headers[] = "Project Role";
$gridList->setHeaders($headers);

// Get team userGroups
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_project_usergroups");
$result = $dbc->execute($q);
$projectGroups = $dbc->fetch($result, TRUE);
foreach ($projectGroups as $group)
{
	$row = array();
	$row[] = $group['name'];
	$gridList->insertRow($row, "pg[".$group['id']."]", isset($roles[$group['id']]));
}

// Create popup
$pp = new popup();
$pp->position("bottom|right");
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>