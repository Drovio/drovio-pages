<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Html\HTMLContent;

// Build the content
$content = new HTMLContent();
$content->build("myPersonalInfo");

// Initialize dbConnection
$dbc = new interDbConnection();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Updated Data
	$q = new dbQuery("107598002", "profile.person");
	$attr = array();
	$attr['firstname'] = $_POST['firstname'];
	$attr['lastname'] = $_POST['lastname'];
	$attr['pid'] = account::getPersonID();
	$result = $dbc->execute($q, $attr);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Get person's information
$q = new dbQuery("1921568048", "profile.person");
$attr = array();
$attr['pid'] = account::getPersonID();
$result = $dbc->execute($q, $attr);
$person = $dbc->fetch($result);


// Header
$headerContent = moduleLiteral::get($moduleID, "lbl_personalInfoHeader");
$header = DOM::create("h4");
DOM::append($header, $headerContent);
$content->append($header);


$sForm = new simpleForm();
$personalDataForm = $sForm->build($moduleID, "personalInfo")->get();
$content->append($personalDataForm);

// Firstname
$title = moduleLiteral::get($moduleID, "lbl_personal_firstName");
$input = $sForm->getInput($type = "text", $name = "firstname", $value = $person['firstname'], $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Lastname
$title = moduleLiteral::get($moduleID, "lbl_personal_lastName");
$input = $sForm->getInput($type = "text", $name = "lastname", $value = $person['lastname'], $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $content->getReport($reportHolder);
//#section_end#
?>