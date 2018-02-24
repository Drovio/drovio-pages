<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Presentation\dataGridList;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get Module Type
	$module_id = $_POST['id'];
	
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

	// Update module info
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "update_module_access_info");
	
	$attr = array();
	$attr['id'] = $_POST['id'];
	$attr['scope'] = $_POST['scope'];
	$attr['status'] = $_POST['status'];
	$success = $dbc->execute($dbq, $attr);
	
	// Clear key types
	$dbq = module::getQuery($moduleID, "clear_module_keytypes");
	$attr = array();
	$attr['id'] = $_POST['id'];
	$success = $dbc->execute($dbq, $attr);
	
	// Set key types
	$dbq = module::getQuery($moduleID, "add_module_keytype");
	$attr = array();
	$attr['id'] = $_POST['id'];
	foreach ($_POST['keytype'] as $type_id => $value)
	{
		$attr['type'] = $type_id;
		$dbc->execute($dbq, $attr);
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
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Create Module Content
$content = new MContent($moduleID);
$content->build("", "moduleEditor");



$module_id = $_GET['id'];
$moduleData = module::info($module_id);


$form = new simpleForm();
$formElement = $form->build($moduleID, "moduleEditor")->get();
$content->append($formElement);

$input = $form->getInput($type = "hidden", $name = "id", $value = $module_id, $class = "", $autofocus = FALSE);
$form->append($input);

// Header
$title = moduleLiteral::get($moduleID, "lbl_editTitle");
$hd = DOM::create("h4", $title);
$form->append($hd);


// Module Scope
$dbc = new dbConnection();
$dbq = new dbQuery("248347356", "units.modules");
$statuses = $dbc->execute($dbq);
$scopeResource = $dbc->to_array($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleScope");
$input = $form->getResourceSelect($name = "scope", $multiple = FALSE, $class = "", $scopeResource , $selectedValue = $moduleData['scope_id']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");



// Module Status
$dbq = module::getQuery($moduleID, "get_module_statuses");
$statuses = $dbc->execute($dbq);
$statusResource = $dbc->toArray($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$input = $form->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $statusResource, $selectedValue = $moduleData['status_id']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Header
$title = moduleLiteral::get($moduleID, "lbl_keyTypes");
$hd = DOM::create("h4", $title);
$form->append($hd);

// Module Status
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