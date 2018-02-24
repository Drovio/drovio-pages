<?php
//#section#[header]
// Module Declaration
$moduleID = 147;

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
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

$dbc = new interDbConnection();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Currency Description
	$empty = (is_null($_POST['description']) || empty($_POST['description']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Currency Description");
		$err = $errFormNtf->addErrorHeader("currencyDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "currencyDesc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Currency Symbol
	$empty = (is_null($_POST['symbol']) || empty($_POST['symbol']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Currency Symbol");
		$err = $errFormNtf->addErrorHeader("currencySymbol_h", $err_header);
		$errFormNtf->addErrorDescription($err, "currencySymbol_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Currency ISO Code
	$empty = (is_null($_POST['iso']) || empty($_POST['iso']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Currency ISO Code");
		$err = $errFormNtf->addErrorHeader("currencyISO_h", $err_header);
		$errFormNtf->addErrorDescription($err, "currencyISO_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("1148047178", "resources.geoloc.currencies");
	$attr = array();
	$attr['id'] = $_POST['currencyID'];
	$attr['description'] = $_POST['description'];
	$attr['symbol'] = $_POST['symbol'];
	$attr['iso'] = $_POST['iso'];
	$attr['base'] = ($_POST['base'] == "on" ? 1 : 0);
	$attr['rate'] = (empty($_POST['rate']) ? 0 : $_POST['rate']);
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Currency Update");
		$err = $errFormNtf->addErrorHeader("currencyUpdate_h", $err_header);
		$errFormNtf->addErrorDescription($err, "currencyUpdate_desc", DOM::create("span", "Error updating currency..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Get country id
$currencyID = $_GET['cid'];

// Get Country Info
$q = new dbQuery("981502147", "resources.geoloc.currencies");
$attr = array();
$attr['id'] = $currencyID;
$result = $dbc->execute_query($q, $attr);
$currencyInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Currency", $moduleID, "editCurrency", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Currency");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Currency ID
$input = $sForm->getInput($type = "hidden", $name = "currencyID", $value = $currencyInfo['id'], $class = "", $autofocus = TRUE);
$frame->append($input);

// Currency Description
$title = DOM::create("span", "Description");
$input = $sForm->getInput($type = "text", $name = "description", $value = $currencyInfo['description'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Currency Symbol
$title = DOM::create("span", "Symbol");
$input = $sForm->getInput($type = "text", $name = "symbol", $value = $currencyInfo['symbol'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// ISO Code
$title = DOM::create("span", "ISO code");
$input = $sForm->getInput($type = "text", $name = "iso", $value = $currencyInfo['codeISO'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Base Indicator
$title = DOM::create("span", "Base");
$input = $sForm->getInput($type = "checkbox", $name = "base", $value = "", $class = "", $autofocus = FALSE);
if ($currencyInfo['isBase'])
	DOM::attr($input, "checked", "checked");
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Call Code
$title = DOM::create("span", "Rate to Base");
$input = $sForm->getInput($type = "text", $name = "rate", $value = $currencyInfo['rateToBase'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>