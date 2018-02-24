<?php
//#section#[header]
// Module Declaration
$moduleID = 151;

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
	
	// Check Timezone Description
	$empty = (is_null($_POST['description']) || empty($_POST['description']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Timezone Description");
		$err = $errFormNtf->addErrorHeader("timezoneDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "timezoneDesc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Timezone Abbreviation
	$empty = (is_null($_POST['abbreviation']) || empty($_POST['abbreviation']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Timezone Abbreviation");
		$err = $errFormNtf->addErrorHeader("timezoneAbbr_h", $err_header);
		$errFormNtf->addErrorDescription($err, "timezoneAbbr_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Timezone Location
	$empty = (is_null($_POST['location']) || empty($_POST['location']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Timezone Location");
		$err = $errFormNtf->addErrorHeader("timezoneLoc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "timezoneLoc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Timezone Deviation
	$empty = (is_null($_POST['deviation']) || empty($_POST['deviation']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Timezone Deviation");
		$err = $errFormNtf->addErrorHeader("timezoneDev_h", $err_header);
		$errFormNtf->addErrorDescription($err, "timezoneDev_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("304672281", "resources.geoloc.timezones");
	$attr = array();
	$attr['id'] = $_POST['timezoneID'];
	$attr['description'] = $_POST['description'];
	$attr['abbreviation'] = $_POST['abbreviation'];
	$attr['location'] = $_POST['location'];
	$attr['deviation'] = $_POST['deviation'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Timezone Update");
		$err = $errFormNtf->addErrorHeader("timezoneUpdate_h", $err_header);
		$errFormNtf->addErrorDescription($err, "timezoneUpdate_desc", DOM::create("span", "Error updating timezone..."));
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
$timezoneID = $_GET['tid'];

// Get Country Info
$q = new dbQuery("1725654320", "resources.geoloc.timezones");
$attr = array();
$attr['id'] = $timezoneID;
$result = $dbc->execute_query($q, $attr);
$timezoneInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Timezone", $moduleID, "editTimezone", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Timezone");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Country ID
$input = $sForm->getInput($type = "hidden", $name = "timezoneID", $value = $timezoneInfo['id'], $class = "", $autofocus = TRUE);
$frame->append($input);

// Country Name
$title = DOM::create("span", "Description");
$input = $sForm->getInput($type = "text", $name = "description", $value = $timezoneInfo['description'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// ISO2A Code
$title = DOM::create("span", "Abbreviation");
$input = $sForm->getInput($type = "text", $name = "abbreviation", $value = $timezoneInfo['abbreviation'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// ISO3A Code
$title = DOM::create("span", "Location");
$input = $sForm->getInput($type = "text", $name = "location", $value = $timezoneInfo['location'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Vehicle Code
$title = DOM::create("span", "Deviation from GMT");
$input = $sForm->getInput($type = "text", $name = "deviation", $value = $timezoneInfo['deviationFromGMT'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>