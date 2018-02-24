<?php
//#section#[header]
// Module Declaration
$moduleID = 148;

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
	$empty = (is_null($_POST['regionName']) || empty($_POST['regionName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Region Name");
		$err = $errFormNtf->addErrorHeader("regionName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "regionName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("1522272994", "resources.geoloc.regions");
	$attr = array();
	$attr['id'] = $_POST['regionID'];
	$attr['name'] = $_POST['regionName'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Region Update");
		$err = $errFormNtf->addErrorHeader("regionName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "regionName_desc", DOM::create("span", "Error updating region..."));
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
$regionID = $_GET['rid'];

// Get Country Info
$q = new dbQuery("1279226986", "resources.geoloc.regions");
$attr = array();
$attr['id'] = $regionID;
$result = $dbc->execute_query($q, $attr);
$regionInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Region", $moduleID, "editRegion", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Region");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Region ID
$input = $sForm->getInput($type = "hidden", $name = "regionID", $value = $regionInfo['id'], $class = "", $autofocus = TRUE);
$frame->append($input);

// Region Name
$title = DOM::create("span", "Region name");
$input = $sForm->getInput($type = "text", $name = "regionName", $value = $regionInfo['name'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>