<?php
//#section#[header]
// Module Declaration
$moduleID = 146;

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
	$empty = (is_null($_POST['friendlyName']) || empty($_POST['friendlyName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = DOM::create("span", "Locale Friendly Name");
		$err = $errFormNtf->addErrorHeader("localeName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "localeName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Locale
	$q = new dbQuery("1171630281", "resources.geoloc.locale");
	$attr = array();
	$attr['locale'] = $_POST['locale'];
	$attr['friendlyName'] = $_POST['friendlyName'];
	$attr['encoding'] = $_POST['encoding'];
	$result = $dbc->execute_query($q, $attr);
	
	// If there is an error in updating the country, show it
	if (!$result)
	{
		$err_header = DOM::create("span", "Locale Update");
		$err = $errFormNtf->addErrorHeader("localeUpdate_h", $err_header);
		$errFormNtf->addErrorDescription($err, "localeUpdate_desc", DOM::create("span", "Error updating locale..."));
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
$locale = $_GET['lid'];

// Get Country Info
$q = new dbQuery("637187577", "resources.geoloc.locale");
$attr = array();
$attr['locale'] = $locale;
$result = $dbc->execute_query($q, $attr);
$localeInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$frame->build("Edit Locale", $moduleID, "editLocale", FALSE);
$sForm = new simpleForm();


// Header
$hd = DOM::create("span", "Edit Locale");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Locale
$input = $sForm->getInput($type = "hidden", $name = "locale", $value = $locale, $class = "", $autofocus = TRUE);
$frame->append($input);

// Friendly Name
$title = DOM::create("span", "Friendly Name");
$input = $sForm->getInput($type = "text", $name = "friendlyName", $value = $localeInfo['friendlyName'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Encoding
$title = DOM::create("span", "Encoding");
$input = $sForm->getInput($type = "text", $name = "encoding", $value = $localeInfo['encoding'], $class = "", $autofocus = TRUE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>