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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Presentation\dataGridList;

$module_id = engine::getVar('id');

// Get module account groups
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_module_accgroups");
$attr = array();
$attr['mid'] = $module_id;
$result = $dbc->execute($dbq, $attr);
$mAccGroups = $dbc->toArray($result, "id", "name");

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

	// Update module info
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "update_module_access_info");
	
	$attr = array();
	$attr['id'] = $module_id;
	$attr['scope'] = $_POST['scope'];
	$attr['status'] = $_POST['status'];
	$success = $dbc->execute($dbq, $attr);
	
	// Clear key types
	$dbq = module::getQuery($moduleID, "clear_module_keytypes");
	$attr = array();
	$attr['id'] = $module_id;
	$success = $dbc->execute($dbq, $attr);
	
	// Set key types
	$dbq = module::getQuery($moduleID, "add_module_keytype");
	$attr = array();
	$attr['id'] = $module_id;
	foreach ($_POST['keytype'] as $type_id => $value)
	{
		$attr['type'] = $type_id;
		$dbc->execute($dbq, $attr);
	}
	
	
	// Get Grant Privileges
	$postGrant = $_POST['acgrp'];
	
	// Get privileges to revoke
	$revoke = array();
	foreach ($mAccGroups as $key => $value)
		if (!isset($postGrant[$key]))
			$revoke[] = $key;
	
	// Get privileges to grant
	$grant = array();
	foreach ($postGrant as $key => $value)
		if (!isset($mAccGroups[$key]))
			$grant[] = $key;
	
	// Revoke Privileges
	if (!empty($revoke))
	{
		$q = module::getQuery($moduleID, "remove_module_from_group");
		$attr = array();
		$attr['ids'] = implode(",", $revoke);
		$attr['mid'] = $module_id;
		$result = $dbc->execute($q, $attr);
	}
	
	// Grant Privileges
	$attr = array();
	$attr['mid'] = $module_id;
	foreach ($grant as $grantGroupID)
	{
		$q = module::getQuery($moduleID, "add_module_to_group");
		$attr['gid'] = $grantGroupID;
		$result = $dbc->execute($q, $attr);
	}
	
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Update");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating the module..."));
		return $errFormNtf->getReport();
	}
	
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

// Get all account groups
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_account_groups");
$accGroups = $dbc->execute($dbq);
foreach ($accGroups as $accGroup)
{
	$row = array();
	$row[] = $accGroup['name'];
	$gridList->insertRow($row, "acgrp[".$accGroup['id']."]", isset($mAccGroups[$accGroup['id']]));
}

// Header
$title = moduleLiteral::get($moduleID, "lbl_keyTypes");
$hd = DOM::create("h4", $title, "", "hd");
$form->append($hd);

// Module Status
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_security_keytypes");
$keyTypes = $dbc->execute($dbq);
$keyTypesResource = $dbc->toArray($keyTypes, "id", "type");

$gridList = new dataGridList();
$keyTypeList = $gridList->build("", TRUE)->get();
$form->append($keyTypeList);

// Set headers
$headers = array();
$headers[] = "Key Type";
$gridList->setHeaders($headers);

$moduleKeyTypes = module::getKeyTypes($module_id);
foreach ($keyTypesResource as $typeID => $typeName)
{
	$row = array();
	$row[] = $typeName;
	$gridList->insertRow($row, "keytype[".$typeID."]", in_array($typeID, $moduleKeyTypes));
}

return $content->getReport();
//#section_end#
?>