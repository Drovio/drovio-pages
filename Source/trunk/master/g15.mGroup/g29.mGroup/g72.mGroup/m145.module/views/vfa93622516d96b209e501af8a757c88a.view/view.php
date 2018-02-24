<?php
//#section#[header]
// Module Declaration
$moduleID = 145;

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
	
	// Check Country Name
	$empty = (is_null($_POST['countryName']) || empty($_POST['countryName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Country Name");
		$err = $errFormNtf->addErrorHeader("countryName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "countryName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("90913340", "resources.geoloc.countries");
	$attr = array();
	$attr['name'] = $_POST['countryName'];
	$attr['iso2a'] = $_POST['iso2a'];
	$attr['iso3a'] = $_POST['iso3a'];
	$attr['itucall'] = $_POST['callCode'];
	$attr['unvehicle'] = $_POST['vehCode'];
	$attr['region_id'] = ($_POST['noRegion'] == "on" ? "NULL" : $_POST['region']);
	$attr['imageName'] = $_POST['imageName'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in adding the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Country Update");
		$err = $errFormNtf->addErrorHeader("countryName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "countryName_desc", DOM::create("span", "Error adding the country..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$frame->build("Add New Country", $moduleID, "addCountry", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Add a new Country");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Country Name
$title = DOM::create("span", "Country name");
$input = $sForm->getInput($type = "text", $name = "countryName", $value = "", $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// ISO2A Code
$title = DOM::create("span", "ISO2A Code");
$input = $sForm->getInput($type = "text", $name = "iso2a", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// ISO3A Code
$title = DOM::create("span", "ISO3A Code");
$input = $sForm->getInput($type = "text", $name = "iso3a", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Vehicle Code
$title = DOM::create("span", "Vehicle Code");
$input = $sForm->getInput($type = "text", $name = "callCode", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Call Code
$title = DOM::create("span", "ITU Calling Code");
$input = $sForm->getInput($type = "text", $name = "vehCode", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// No Region
$title = DOM::create("span", "No Region");
$input = $sForm->getInput($type = "checkbox", $name = "noRegion", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Get all regions
$q = new dbQuery("1085838709", "resources.geoloc.regions");
$result = $dbc->execute_query($q, $attr = array());
$regionResource = $dbc->to_array($result, "id", "name");

// Country Region
$title = DOM::create("span", "Region");
$input = $sForm->getResourceSelect($name = "region", $multiple = FALSE, $class = "", $regionResource, $selectedValue = NULL);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Image Name
$title = DOM::create("span", "Flag Image Name");
$input = $sForm->getInput($type = "text", $name = "imageName", $value = "Default.png", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>