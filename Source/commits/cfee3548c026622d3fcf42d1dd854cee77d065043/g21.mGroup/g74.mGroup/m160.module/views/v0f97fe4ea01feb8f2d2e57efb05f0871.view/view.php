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
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Html\HTMLContent;

// Build the content
$content = new HTMLContent();
$content->build("myUsernameManager");

// Initialize dbConnection
$dbc = new interDbConnection();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	
	// Check if username is unique
	$q = new dbQuery("1758928587", "profile.person");
	$attr = array();
	$attr['username'] = $_POST['username'];
	$result = $dbc->execute($q, $attr);
	$row = $dbc->fetch($result);
	$count = $row['count'];
	if ($count > 0 && $_POST['username'] != account::getUsername())
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global.dictionary", "username");
		$err = $errFormNtf->addErrorHeader("username_h", $err_header);
		$errFormNtf->addErrorDescription($err, "username_desc", $errFormNtf->getErrorMessage("err.exists"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Updated Username
	$q = new dbQuery("236277468", "profile.person");
	$attr = array();
	$attr['username'] = $_POST['username'];
	$attr['pid'] = account::getPersonID();
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in updating the username, show it
	if (!$result)
	{
		$err_header = literal::get("global.dictionary", "username");
		$err = $errFormNtf->addErrorHeader("username_h", $err_header);
		$errFormNtf->addErrorDescription($err, "username_desc", DOM::create("span", "Error updating username..."));
		return $errFormNtf->getReport();
	}
	
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
$headerContent = moduleLiteral::get($moduleID, "lbl_usernameManagerHeader");
$header = DOM::create("h4");
DOM::append($header, $headerContent);
$content->append($header);


$sForm = new simpleForm();
$personalDataForm = $sForm->build($moduleID, "usernameManager")->get();
$content->append($personalDataForm);

// Username
$title = moduleLiteral::get($moduleID, "lbl_newUsername");
$input = $sForm->getInput($type = "text", $name = "username", $value = $person['username'], $class = "", $autofocus = FALSE);
$inputRow = $sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $content->getReport($reportHolder);
//#section_end#
?>