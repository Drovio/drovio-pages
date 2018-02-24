<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\components\sql\dvbQuery;
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
	
	if (empty($_POST['description']))
	{
		$hasError = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_commitDescription");
		$err = $errFormNtf->addErrorHeader("lblDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDesc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	if ($hasError)
		return $errFormNtf->getReport();
	
	// Commit SQL Query
	//$dvbq = new dvbQuery($_POST['domain'], $_POST['qid']);
	//$status = $dvbq->commit($_POST['description']);
	
	// If there is an error in creating the folder, show it
	if (!$status)
	{
		/*
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", DOM::create("span", "Error creating folder..."));
		return $errFormNtf->getReport();
		*/
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
$frame->build("Commit SQL Query", $moduleID, "commitQuery", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_commitObject");
$frame->append($hd);

// Object Library
$input = $sForm->getInput($type = "hidden", $name = "qid", $value = $_GET['qid']);
$frame->append($input);

// Object Package
$input = $sForm->getInput($type = "hidden", $name = "domain", $value = $_GET['domain']);
$frame->append($input);

// Commit Description
$title = moduleLiteral::get($moduleID, "lbl_commitDescription");
$input = $sForm->getTextarea($name = "description", $value = "", $class = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>