<?php
//#section#[header]
// Module Declaration
$moduleID = 68;

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
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\model\units\domain\Udomain;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$exists = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Title
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if (!$empty)
	{
		// Check if domain exists
		$dbq = new dbQuery("758802961", "units.domains");
		$dbc = new interDbConnection;
		
		$attr = array();
		$attr['name'] = $_POST['title'];
		$result = $dbc->execute_query($dbq, $attr);

		if ($dbc->get_num_rows($result) > 0)
			$exists = TRUE;
	}
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addErrorHeader("lblDomain_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDomain_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	else if ($exists)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addErrorHeader("lblDomain_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDomain_desc", $errFormNtf->getErrorMessage("err.exists"));
	}
	
	// Check Path
	$empty = is_null($_POST['path']) || empty($_POST['path']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Theme
	$theme_empty = is_null($_POST['theme']) || empty($_POST['theme']);
	if ($theme_empty)
		$_POST['theme'] = "default";
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$success = Udomain::create($_POST['title'], $_POST['theme'], $_POST['path']);
	
	// If there is an error in creating the library, show it
	if (!$success)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addErrorHeader("lblDomain_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDomain_desc", DOM::create("span", "Error creating domain..."));
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
$frame->build("Create New Domain", $moduleID, "createDomain", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_createDomain");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Domain Title
$title = moduleLiteral::get($moduleID, "lbl_domain");
$input = $sForm->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Domain Path
$titleContent = moduleLiteral::get($moduleID, "lbl_folder");
$titleNote = DOM::create("span", " (/_sbd_)");
$title = DOM::create("span");
DOM::append($title, $titleContent);
DOM::append($title, $titleNote);
$input = $sForm->getInput($type = "text", $name = "path", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Domain Theme
$title = moduleLiteral::get($moduleID, "lbl_theme");
$input = $sForm->getInput($type = "text", $name = "theme", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>