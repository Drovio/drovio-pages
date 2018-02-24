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
use \API\Developer\model\units\domain\Upage;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Title
	$empty = is_null($_POST['folder']) || empty($_POST['folder']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Theme
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global::dictionary", "name");
		$err = $errFormNtf->addErrorHeader("lblName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$success = Upage::create($_POST['folder'], $_POST['title']);
	
	// If there is an error in creating the page, show it
	if (!$success)
	{
		$err_header = literal::get("global::dictionary", "name");
		$err = $errFormNtf->addErrorHeader("lblPage_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblPage_desc", DOM::create("span", "Error creating page..."));
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
$frame->build("Create New Page", $moduleID, "createPage", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_createPage");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Get all folders
$dbc = new interDbConnection();
$dbq = new dbQuery("737200095", "units.domains.folders");
$folders = $dbc->execute_query($dbq);
	
// Create domain tree on the sidebar
$folderResource = array();
while ($folder = $dbc->fetch($folders))
{
	$folderTitle = ($folder['name'] == "" ? $folder['domain'] : $folder['name']);
	$parentTitle = $folderResource[$folder['parent_id']];
	$folderID = $folder['id'];
	$folderResource[$folderID] = ($parentTitle == "" ? "" : $parentTitle." > ").$folderTitle;
}

// Parent Folder
$title = moduleLiteral::get($moduleID, "lbl_folder");
$input = $sForm->getResourceSelect($name = "folder", $multiple = FALSE, $class = "", $folderResource, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Page Title
$titleContent = literal::get("global::dictionary", "name");
$titleNote = DOM::create("span", " (.php)");
$title = DOM::create("span");
DOM::append($title, $titleContent);
DOM::append($title, $titleNote);
$input = $sForm->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>