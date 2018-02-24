<?php
//#section#[header]
// Module Declaration
$moduleID = 342;

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
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\privileges;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;

$accountID = engine::getVar('aid');
$dbc = new dbConnection();

// Get active account groups
$q = module::getQuery($moduleID, "get_account_groups");
$groups = $dbc->execute($q);
$groupNames = $dbc->toArray($groups, "id", "name");
	
// get participation of user active groups
$q = module::getQuery($moduleID, "get_account_to_group");
$attr = array();
$attr['id'] = $accountID;
$groups = $dbc->execute($q, $attr);
$accountGroups = $dbc->toArray($groups, "id", "name");
	
if (engine::isPost())
{
	// Get old and new groups
	$oldGroups = $accountGroups;
	$newGroups = engine::getVar('grant');
	
	// Select which groups to grant access and which to revoke
	$grant = array_keys($newGroups);
	$revoke = array_diff(array_keys($accountGroups), $grant);
	
	// Revoke access
	foreach ($revoke as $groupID)
		privileges::leaveAccountFromGroupID($accountID, $groupID);
		
	// Grant access
	foreach ($newGroups as $groupID => $groupName)
		privileges::addAccountToGroupID($accountID, $groupID);
		
	$successNotification = new formNotification();
	$successNotification->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Description
	$message = $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Create Module Page
$pageContent = new MContent($moduleID);
$pageContent->build("", "accGroupEditor");

$form = new simpleForm();
$gEditorForm = $form->build()->engageModule($moduleID, "accGroupEditor")->get();
$pageContent->append($gEditorForm);
 
// userId (Hidden)
$input = $form->getInput("hidden", "aid", $accountID, $class = "", $autofocus = FALSE);
$form->append($input);

// Form Header
$attr = array();
$attr['id'] = $accountID;
$headerContent = moduleLiteral::get($moduleID, "lbl_groupsInfo", $attr);
$header = DOM::create('h3', $headerContent, "", "lhd");
$form->append($header);

$formContentWrapper = DOM::create("div", "", "", "userGroupsFormContents");
$form->append($formContentWrapper);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($formContentWrapper, $glist);

$headers = array();
$headers[] = "Group Name";
$dtGridList->setHeaders($headers);

// Present Info
foreach ($groupNames as $groupID => $groupName)
{
	$gridRow = array();
	$gridRow[] = DOM::create("span", $groupName);
	$dtGridList->insertRow($gridRow, 'grant['.$groupID.']', isset($accountGroups[$groupID]));
}

// Return output
return $pageContent->getReport();
//#section_end#
?>