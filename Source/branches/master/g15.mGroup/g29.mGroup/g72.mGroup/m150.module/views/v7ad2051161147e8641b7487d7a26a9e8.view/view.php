<?php
//#section#[header]
// Module Declaration
$moduleID = 150;

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
	$empty = (is_null($_POST['uniName']) || empty($_POST['uniName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Language Name");
		$err = $errFormNtf->addErrorHeader("langName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "langName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Country
	$q = new dbQuery("1313391768", "resources.geoloc.languages");
	$attr = array();
	$attr['id'] = $_POST['langID'];
	$attr['uniDescription'] = $_POST['uniName'];
	$attr['nativeDescription'] = $_POST['natName'];
	$attr['iso2a3'] = $_POST['iso2a3'];
	$attr['iso1a2'] = $_POST['iso1a2'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Language Update");
		$err = $errFormNtf->addErrorHeader("langUpdate_h", $err_header);
		$errFormNtf->addErrorDescription($err, "langUpdate_desc", DOM::create("span", "Error updating language..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Get language id
$langID = $_GET['lid'];

// Get Country Info
$q = new dbQuery("1863002524", "resources.geoloc.languages");
$attr = array();
$attr['id'] = $langID;
$result = $dbc->execute_query($q, $attr);
$languageInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Language", $moduleID, "editLanguage", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Language");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Country ID
$input = $sForm->getInput($type = "hidden", $name = "langID", $value = $langID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Universal Language Name (English)
$title = DOM::create("span", "Universal Name");
$input = $sForm->getInput($type = "text", $name = "uniName", $value = $languageInfo['uniDescription'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Native Language Name
$title = DOM::create("span", "Native Name");
$input = $sForm->getInput($type = "text", $name = "natName", $value = $languageInfo['nativeDescription'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// ISO2A Code
$title = DOM::create("span", "ISO2 / A3 Code");
$input = $sForm->getInput($type = "text", $name = "iso2a3", $value = $languageInfo['languageCode_ISO2_A3'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// ISO3A Code
$title = DOM::create("span", "ISO1 / A2 Code");
$input = $sForm->getInput($type = "text", $name = "iso1a2", $value = $languageInfo['languageCode_ISO1_A2'], $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>