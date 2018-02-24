<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
use \UI\Presentation\popups\popup;
use \UI\Presentation\dataGridList;

$module_id = engine::getVar('mid');

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
	
	// Update Module Permission Groups
	// Get Grant Privileges
	$postGrant = $_POST['grant'];
	
	// Get privileges to revoke
	foreach ($mAccGroups as $groupID => $value)
		if (!isset($postGrant[$groupID]))
			privileges::removeModulePermissionGroup($module_id, $groupID);
	
	// Get privileges to grant
	foreach ($postGrant as $groupID => $value)
		if (!isset($mAccGroups[$groupID]))
			privileges::addModulePermissionGroup($module_id, $groupID);
	
	
	// Success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Create Module Page
$pageContent = new MContent();
$moduleInfoContent = $pageContent->build("mi_".$module_id, "moduleInfo")->get();

$titleContent = moduleLiteral::get($moduleID, "lbl_moduleInfo_title");
$title = DOM::create("p", $titleContent);
$pageContent->append($title);

// Get Page module info
$moduleInfo = module::info($module_id);

$sForm = new simpleForm();
$formElement = $sForm->build()->engageModule($moduleID, "moduleInfo")->get();
$pageContent->append($formElement);

$input = $sForm->getInput($type = "hidden", $name = "mid", $value = $module_id, $class = "", $autofocus = FALSE);
$sForm->append($input);


// Module Scope
$scopeResource = array();
$moduleScopes = module::getModuleScopes();
foreach ($moduleScopes as $scopeInfo)
	$scopeResource[$scopeInfo['id']] = $scopeInfo['description'];
$title = moduleLiteral::get($moduleID, "lbl_moduleScope");
$input = $sForm->getResourceSelect($name = "scope", $multiple = FALSE, $class = "", $scopeResource , $selectedValue = $moduleInfo['scope_id']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);



// Module Status
$statusResource = array();
$moduleStatuses = module::getModuleStatus();
foreach ($moduleStatuses as $statusInfo)
	$statusResource[$statusInfo['id']] = $statusInfo['description'];
$title = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$input = $sForm->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $statusResource, $selectedValue = $moduleInfo['status_id']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);

// Get module user groups
$moduleUserGroups = privileges::getModuleUserGroups($module_id);

// User Groups
$gridList = new dataGridList();
$userGroupList = $gridList->build("moduleUserGroups", TRUE)->get();
$sForm->append($userGroupList);

$headers = array();
$headers[] = "User Group";
$gridList->setHeaders($headers);
$pmGroups = privileges::getPermissionGroups();
foreach ($pmGroups as $groupID => $groupName)
{
	$contents = array();
	$contents[] = $groupName;
	$gridList->insertRow($contents, "grant[".$groupID.']', isset($moduleUserGroups[$groupID]));
}


// Build the popup
$popup = new popup();
$popup->position("right|top");
$popup->build($moduleInfoContent);

// Return output
return $popup->getReport();
//#section_end#
?>