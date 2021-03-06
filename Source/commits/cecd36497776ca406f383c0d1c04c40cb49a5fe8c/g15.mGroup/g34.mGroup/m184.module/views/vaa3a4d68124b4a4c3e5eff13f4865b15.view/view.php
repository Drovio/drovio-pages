<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Presentation\popups\popup;
use \UI\Presentation\dataGridList;

$module_id = $_REQUEST['mid'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Status
	$empty = is_null($_POST['status']) || empty($_POST['status']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_moduleStatus");
		$err = $errFormNtf->addErrorHeader("motuleStatus_h", $err_header);
		$errFormNtf->addErrorDescription($err, "motuleStatus_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Scope
	$empty = is_null($_POST['scope']) || empty($_POST['scope']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_moduleScope");
		$err = $errFormNtf->addErrorHeader("motuleScope_h", $err_header);
		$errFormNtf->addErrorDescription($err, "motuleScope_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	$dbc = new dbConnection();
	
	// Update Module Info
	$dbq = new dbQuery("702911997", "units.modules");
	
	$attr = array();
	$attr['id'] = $_POST['id'];
	$attr['scope'] = $_POST['scope'];
	$attr['status'] = $_POST['status'];
	$success = $dbc->execute($dbq, $attr);
	
	// Update Module user Groups
	$dbq = new dbQuery("999274607", "security.privileges.user");
	$groupsResource = $dbc->execute($dbq);
	$revokeModules = array();
	while ($row = $dbc->fetch($groupsResource))
	{
		$attr = array();
		$attr['gid'] = $row['id'];
		
		// Grand or revoke access
		if (isset($_POST['userGroup'][$row['id']]))
		{
			$attr['mid'] = $_POST['id'];
			$dbq = new dbQuery("168706013", "security.privileges");
		}
		else
		{
			$attr['ids'] = $_POST['id'];
			$dbq = new dbQuery("539161284", "security.privileges");
		}
		
		// Execute
		$result = $dbc->execute($dbq, $attr);
	}
	
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Update");
		$err = $errFormNtf->addErrorHeader("module_h", $err_header);
		$errFormNtf->addErrorDescription($err, "module_desc", DOM::create("span", "Error updating the module..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
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


$dbc = new dbConnection();

// Get Page Info
$dbq = new dbQuery("1550577305", "units.modules");
$attr = array();
$attr['id'] = $module_id;
$result = $dbc->execute($dbq, $attr);
$moduleData = $dbc->fetch($result);


$sForm = new simpleForm();
$formElement = $sForm->build($moduleID, "moduleInfo")->get();
$pageContent->append($formElement);

$input = $sForm->getInput($type = "hidden", $name = "id", $value = $module_id, $class = "", $autofocus = FALSE);
$sForm->append($input);


// Module Scope
$dbq = new dbQuery("248347356", "units.modules");
$statuses = $dbc->execute($dbq);
$scopeResource = $dbc->toArray($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleScope");
$input = $sForm->getResourceSelect($name = "scope", $multiple = FALSE, $class = "", $scopeResource , $selectedValue = $moduleData['scope']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);



// Module Status
$dbq = new dbQuery("413544198", "units.modules");
$statuses = $dbc->execute($dbq);
$statusResource = $dbc->toArray($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$input = $sForm->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $statusResource, $selectedValue = $moduleData['status']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);

// Get module user groups
$dbq = new dbQuery("1764235324", "security.privileges.user");
$attr = array();
$attr['mid'] = $module_id;
$result = $dbc->execute($dbq, $attr);
$moduleUserGroups = $dbc->toArray($result, "id", "name");

// User Groups
$gridList = new dataGridList();
$userGroupList = $gridList->build("moduleUserGroups", TRUE)->get();
$sForm->append($userGroupList);

$headers = array();
$headers[] = "User Group";
$gridList->setHeaders($headers);

$dbq = new dbQuery("999274607", "security.privileges.user");
$groupsResource = $dbc->execute($dbq);
while ($row = $dbc->fetch($groupsResource))
{
	$contents = array();
	$contents[] = $row['name'];
	$checked = array_key_exists($row['id'], $moduleUserGroups);
	$gridList->insertRow($contents, "userGroup[".$row['id'].']', $checked);
}


// Build the popup
$popup = new popup();
//$popup->parent("columnContainer");
$popup->position("right|top");
$popup->build($moduleInfoContent);

// Return output
return $popup->getReport();
//#section_end#
?>