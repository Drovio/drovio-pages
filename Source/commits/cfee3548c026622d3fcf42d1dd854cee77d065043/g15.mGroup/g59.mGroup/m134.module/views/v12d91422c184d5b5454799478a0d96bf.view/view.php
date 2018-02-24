<?php
//#section#[header]
// Module Declaration
$moduleID = 134;

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
importer::import("ESS", "Protocol");
importer::import("ESS", "Prototype");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\HTMLServerReport;
use \ESS\Prototype\html\PopupPrototype;
use \API\Developer\components\ebuilder\ebObject;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	if (inputValidator::checkNotset($_POST['description']))
	{
		$hasError = TRUE;
		$headerMessage = moduleLiteral::get($moduleID, "lbl_commitDescription");
		$header = $errorNtf->addErrorHeader("commitDesc_h", $headerMessage);
		$errorNtf->addErrorDescription($header, "commitDesc_desc", $errorNtf->getErrorMessage("err.required"));
	}
	
	if ($hasError)
		return $errorNtf->getReport();
	
	// Commit SDK Object
	$ebObj = new ebObject($_POST['lib'], $_POST['pkg'], $_POST['ns'], $_POST['oid']);
	$status = $ebObj->commit($_POST['description']);
	
	// Return form report
	return simpleForm::getSubmitContent(ServerReporter::statusReport($status, "", FALSE), $reset = TRUE);
}


// Build the frame
$frame = new dialogFrame();
$frame->build(moduleLiteral::get($moduleID, "hd_commitObject", FALSE), $moduleID, "commitObject", FALSE);

$form = new simpleForm("ebuilderCommit");

// Header
$hd = moduleLiteral::get($moduleID, "hd_commitObject");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

$text = moduleLiteral::get($moduleID, "lbl_commitObject");
$frame->append($text);

// Create Hidden Values
$hidden = $form->getInput($type = "hidden", $name = "lib", $value = $_GET['lib']);
$frame->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "pkg", $value = $_GET['pkg']);
$frame->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "ns", $value = $_GET['ns']);
$frame->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "oid", $value = $_GET['oid']);
$frame->append($hidden);

$commitDescription = $form->getTextarea($name = "description", $value = "", $class = "");
$title = moduleLiteral::get($moduleID, "lbl_commitDescription");
$descRow = $form->buildRow($title, $commitDescription, $required = TRUE, $notes = "");
$frame->append($descRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>