<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\privileges;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Presentation\dataGridList;

// Get module id
$module_id = engine::getVar('id');

// Get all permission groups
$pmGroups = privileges::getPermissionGroups();
$modulePMGroups = privileges::getModuleUserGroups($module_id);
$mAccGroups = array();
foreach ($modulePMGroups as $groupID)
	$mAccGroups[$groupID] = $pmGroups[$groupID];

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Status
	if (empty($_POST['status']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_moduleStatus");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Scope
	if (empty($_POST['scope']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_moduleScope");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();

	// Update module access info
	module::updateAccessInfo($module_id, $_POST['scope'], $_POST['status']);
	
	// Update module key types
	$keyTypeIDs = array();
	foreach ($_POST['keytype'] as $typeID => $value)
		$keyTypeIDs[] = $typeID;
	module::updateKeyTypes($module_id, $keyTypeIDs);
	
	
	// Get Grant Privileges
	$postGrant = $_POST['acgrp'];
	
	// Get privileges to revoke
	foreach ($mAccGroups as $groupID => $value)
		if (!isset($postGrant[$groupID]))
			privileges::removeModulePermissionGroup($module_id, $groupID);
	
	// Get privileges to grant
	foreach ($postGrant as $groupID => $value)
		if (!isset($mAccGroups[$groupID]))
			privileges::addModulePermissionGroup($module_id, $groupID);
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Create Module Content
$content = new MContent($moduleID);
$content->build("", "moduleEditor");

// Get module info
$moduleData = module::info($module_id);

$form = new simpleForm();
$formElement = $form->build()->engageModule($moduleID, "moduleEditor")->get();
$content->append($formElement);

$input = $form->getInput($type = "hidden", $name = "id", $value = $module_id, $class = "", $autofocus = FALSE);
$form->append($input);

// Header
$title = moduleLiteral::get($moduleID, "lbl_editTitle");
$hd = DOM::create("h4", $title, "", "hd");
$form->append($hd);


// Module Scope
$scopes = module::getModuleScopes();
foreach ($scopes as $scope)
	$scopeResource[$scope['id']] = $scope['description'];

$title = moduleLiteral::get($moduleID, "lbl_moduleScope");
$input = $form->getResourceSelect($name = "scope", $multiple = FALSE, $class = "", $scopeResource , $selectedValue = $moduleData['scope_id']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");



// Module Status
$statuses = module::getModuleStatus();
foreach ($statuses as $status)
	$statusResource[$status['id']] = $status['description'];

$title = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$input = $form->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $statusResource, $selectedValue = $moduleData['status_id']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Header
$title = moduleLiteral::get($moduleID, "lbl_accountGroups");
$hd = DOM::create("h4", $title, "", "hd");
$form->append($hd);

// Account groups
$gridList = new dataGridList();
$groupList = $gridList->build("", TRUE)->get();
$form->append($groupList);

// Set headers
$headers = array();
$headers[] = "Group Name";
$gridList->setHeaders($headers);

// Get all permission groups
$pmGroups = privileges::getPermissionGroups();
foreach ($pmGroups as $groupID => $groupName)
{
	$row = array();
	$row[] = $groupName;
	$gridList->insertRow($row, "acgrp[".$groupID."]", isset($mAccGroups[$groupID]));
}

// Header
$title = moduleLiteral::get($moduleID, "lbl_keyTypes");
$hd = DOM::create("h4", $title, "", "hd");
$form->append($hd);

// Module Key Types
$gridList = new dataGridList();
$keyTypeList = $gridList->build("", TRUE)->get();
$form->append($keyTypeList);

// Set headers
$headers = array();
$headers[] = "Key Type";
$gridList->setHeaders($headers);

$moduleKeyTypes = module::getKeyTypes($module_id);
$allKeyTypes = module::getAllKeyTypes();
foreach ($allKeyTypes as $typeID => $typeName)
{
	$row = array();
	$row[] = $typeName;
	$gridList->insertRow($row, "keytype[".$typeID."]", in_array($typeID, $moduleKeyTypes));
}

return $content->getReport();
//#section_end#
?>