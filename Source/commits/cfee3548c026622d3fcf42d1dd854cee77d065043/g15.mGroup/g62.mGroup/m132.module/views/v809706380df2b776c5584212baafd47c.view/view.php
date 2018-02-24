<?php
//#section#[header]
// Module Declaration
$moduleID = 132;

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
use \API\Developer\components\sdk\sdkObject;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	if (empty($_POST['description']))
	{
		$hasError = TRUE;
		$header = $errorNtf->addErrorHeader("err", "Commit Description");
		$errorNtf->addErrorDescription($header, "commitDescription", "Required Field.", $extra = "");
	}
	if ($hasError)
		return $errorNtf->getReport();
	
	// Commit SDK Object
	$appObj = new appObject($_POST['lib'], $_POST['pkg'], $_POST['ns'], $_POST['oid']);
	$status = $appObj->commit($_POST['description']);
	
	// Return form report
	return simpleForm::getSubmitContent(ServerReporter::statusReport($status, "", FALSE), $reset = TRUE);
}

// Create Popup Notification
$ntf = new notification;
$notification = $ntf->build($type = "default", $header = FALSE, $footer = FALSE)->get();

$header = moduleLiteral::get($moduleID, "lbl_commitObject");
$ntf->append($header);

// Create Commit Form
$form = new simpleForm("appObjCommit");
$commitForm = $form->build($moduleID, "commitObject")->get();
$ntf->append($commitForm);

// Create Hidden Values
$hidden = $form->getInput($type = "hidden", $name = "lib", $value = $_GET['lib']);
$form->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "pkg", $value = $_GET['pkg']);
$form->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "ns", $value = $_GET['ns']);
$form->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "oid", $value = $_GET['oid']);
$form->append($hidden);

$commitDescription = $form->getTextarea($name = "description", $value = "", $class = "");
$form->insertRow(moduleLiteral::get($moduleID, "lbl_commitDescription"), $commitDescription, $required = TRUE, $notes = "");


//---------- AUTO-GENERATED CODE ----------//
// Clear report stack
report::clear();

// redWIDE status report
report::addContent($notification, "popup");
return report::get();
//#section_end#
?>