<?php
//#section#[header]
// Module Declaration
$moduleID = 152;

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
	$empty = (is_null($_POST['townDescription']) || empty($_POST['townDescription']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Town Name");
		$err = $errFormNtf->addErrorHeader("townName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "townName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("1502962542", "resources.geoloc.towns");
	$attr = array();
	$attr['id'] = $_POST['townID'];
	$attr['description'] = $_POST['townDescription'];
	$attr['countryID'] = $_POST['countryID'];
	$attr['latitude'] = empty($_POST['latitude']) ? 0 : $_POST['latitude'];
	$attr['longitude'] = empty($_POST['longitude']) ? 0 : $_POST['longitude'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Town Update");
		$err = $errFormNtf->addErrorHeader("townUpdate_h", $err_header);
		$errFormNtf->addErrorDescription($err, "townUpdate_desc", DOM::create("span", "Error updating town..."));
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
$townID = $_GET['tid'];

// Get Country Info
$q = new dbQuery("217173525", "resources.geoloc.towns");
$attr = array();
$attr['id'] = $townID;
$result = $dbc->execute_query($q, $attr);
$townInfo = $dbc->fetch($result);
print_r($townInfo);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Town", $moduleID, "editTown", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Town");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Town ID
$input = $sForm->getInput($type = "hidden", $name = "townID", $value = $townID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Town Description
$title = DOM::create("span", "Town name");
$input = $sForm->getInput($type = "text", $name = "townDescription", $value = $townInfo['description'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Country Selection
// Get all Countries
$q = new dbQuery("1434209549", "resources.geoloc.countries");
$result = $dbc->execute_query($q, $attr = array());
$countriesResource = $dbc->to_array($result, "id", "countryName");

$title = DOM::create("span", "Country");
$input = $sForm->getResourceSelect($name = "countryID", $multiple = FALSE, $class = "", $countriesResource, $selectedValue = $townInfo['country_id']);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Latitude
$title = DOM::create("span", "Town latitude");
$input = $sForm->getInput($type = "text", $name = "latitude", $value = $townInfo['latitude'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Longitude
$title = DOM::create("span", "Town longitude");
$input = $sForm->getInput($type = "text", $name = "longitude", $value = $townInfo['longitude'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>