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
	$empty = (is_null($_POST['scope']) || empty($_POST['scope']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_literalScope");
		$err = $errFormNtf->addErrorHeader("lbl_literalScope_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_literalScope_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$dbc = new interDbConnection();
	$dbq = new dbQuery("1697604621", "resources.literals");
	
	$attr = array();
	$attr['scope'] = $_POST['scope'];
	$attr['module_id'] = "NULL";
	$status = $dbc->execute($dbq, $attr);
	
	// If there is an error in creating the object, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_newScope");
		$err = $errFormNtf->addErrorHeader("lbl_newScope_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_newScope_desc", DOM::create("span", "Error creating literal scope..."));
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
$frameTitle = moduleLiteral::get($moduleID, "lbl_newScope", FALSE);
$frame->build($frameTitle, $moduleID, "createNewScope", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_newScope");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Literal Scope
$title = moduleLiteral::get($moduleID, "lbl_literalScope");
$input = $sForm->getInput($type = "text", $name = "scope", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>