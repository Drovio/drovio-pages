<?php
//#section#[header]
// Module Declaration
$moduleID = 38;

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
use \API\Developer\model\units\modules\Umodule;
use \API\Developer\model\units\modules\Uauxiliary;
use \API\Developer\components\moduleObject;
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
	
	// Get POST Variables
	$moduleId = $_POST['moduleId'];
	$description = $_POST['description'];
	$auxSeed = $_POST['auxSeed'];
	
	// Check Description
	$empty = is_null($_POST['description']) || empty($_POST['description']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_description");
		$err = $errFormNtf->addErrorHeader("lblDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDesc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$moduleObject = new moduleObject($moduleId);
	$module = $moduleObject->getModule($auxTitle = "", $auxSeed);
	$module->commit($description);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

//__________ [Page GET Variables] __________//
$moduleId = $_GET['moduleId'];
$auxSeed = $_GET['auxSeed'];
$moduleName = $_GET['moduleName'];

// Build the frame
$frame = new dialogFrame();
$frame->build("Commit Module : ".$moduleName, $moduleID, "", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "info.deployModulePrompt");
$frame->append($hd);

// ModuleID
$input = $sForm->getInput($type = "hidden", $name = "moduleId", $moduleId, $class = "", $autofocus = FALSE);
$frame->append($input);

// Auxiliary Seed
$input = $sForm->getInput($type = "hidden", $name = "auxSeed", $auxSeed, $class = "", $autofocus = FALSE);
$frame->append($input);

// Commit Description
$title = moduleLiteral::get($moduleID, "lbl_description");
$input = $sForm->getTextarea($name = "description", $value = "", $class = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>