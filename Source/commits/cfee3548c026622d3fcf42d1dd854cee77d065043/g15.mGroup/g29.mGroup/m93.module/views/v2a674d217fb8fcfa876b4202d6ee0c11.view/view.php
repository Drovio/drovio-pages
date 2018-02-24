<?php
//#section#[header]
// Module Declaration
$moduleID = 93;

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
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Package Name
	$empty = (is_null($_POST['name']) || empty($_POST['name']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_literalName");
		$err = $errFormNtf->addErrorHeader("lbl_literalName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_literalName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Object Name
	$empty = (is_null($_POST['value']) || empty($_POST['value']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_literalValue");
		$err = $errFormNtf->addErrorHeader("lbl_literalValue_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_literalValue_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$static = (isset($_POST['static']) ? TRUE : FALSE);
	$status = literal::add($_POST['scope'], $_POST['name'], $_POST['value'], $_POST['description'], $static);
	
	// If there is an error in creating the object, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_createLiteral");
		$err = $errFormNtf->addErrorHeader("hd_createLiteral_h", $err_header);
		$errFormNtf->addErrorDescription($err, "hd_createLiteral_desc", DOM::create("span", "Error creating Literal..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$frameTitle = moduleLiteral::get($moduleID, "lbl_createNewLiteral", FALSE);
$frame->build($frameTitle, $moduleID, "createNewLiteral", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "hd_createLiteral");
$hdr = DOM::create("h3", $_GET['scope']);
$frame->append($hdr);

// Literal Scope

// Get all available Scopes
$dbq = new dbQuery("251170707", "resources.literals");
$dbc = new interDbConnection();
$result = $dbc->execute($dbq);
$literalScopes = $dbc->toArray($result, "scope", "scope");

// Hidden scope
$input = $sForm->getInput($type = "hidden", $name = "scope", $value = $_GET['scope'], $class = "", $autofocus = FALSE);
$frame->append($input);

// Literal Name
$title = moduleLiteral::get($moduleID, "lbl_literalName");
$input = $sForm->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Literal description
$title = moduleLiteral::get($moduleID, "lbl_literalDescription");
$input = $sForm->getTextarea($name = "description", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Literal value
$title = moduleLiteral::get($moduleID, "lbl_literalValue");
$input = $sForm->getTextarea($name = "value", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Static Literal
$title = moduleLiteral::get($moduleID, "lbl_staticLiteral");
$input = $sForm->getInput($type = "checkbox", $name = "static", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>