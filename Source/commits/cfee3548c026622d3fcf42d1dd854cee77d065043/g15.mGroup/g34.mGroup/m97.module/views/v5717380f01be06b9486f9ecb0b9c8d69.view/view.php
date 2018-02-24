<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Html\HTMLContent;
use \UI\Notifications\notification;

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

	$dbc = new interDbConnection();
	$dbq = new dbQuery("702911997", "units.modules");
	
	$attr = array();
	$attr['id'] = $_POST['id'];
	$attr['scope'] = $_POST['scope'];
	$attr['status'] = $_POST['status'];
	$success = $dbc->execute_query($dbq, $attr);
	
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


// Create Module Content
$content = new HTMLContent();
$content->build("", "moduleEditor");



$dbc = new interDbConnection();
$module_id = $_GET['id'];

// Get Page Info
$dbq = new dbQuery("1550577305", "units.modules");
$attr = array();
$attr['id'] = $module_id;
$result = $dbc->execute_query($dbq, $attr);
$moduleData = $dbc->fetch($result);


$sForm = new simpleForm();
$formElement = $sForm->build($moduleID, "moduleEditor")->get();
$content->append($formElement);

$input = $sForm->getInput($type = "hidden", $name = "id", $value = $module_id, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Header
$hdContent = moduleLiteral::get($moduleID, "lbl_editTitle");
$hd = DOM::create("h4");
DOM::append($hd, $hdContent);
$sForm->append($hd);


// Module Scope
$dbq = new dbQuery("248347356", "units.modules");
$statuses = $dbc->execute_query($dbq);
$scopeResource = $dbc->to_array($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleScope");
$input = $sForm->getResourceSelect($name = "scope", $multiple = FALSE, $class = "", $scopeResource , $selectedValue = $moduleData['scope']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);



// Module Status
$dbq = new dbQuery("413544198", "units.modules");
$statuses = $dbc->execute_query($dbq);
$statusResource = $dbc->to_array($statuses, "id", "description");

$title = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$input = $sForm->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $statusResource, $selectedValue = $moduleData['status']);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($inputRow);


return $content->getReport();
//#section_end#
?>