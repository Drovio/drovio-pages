<?php
//#section#[header]
// Module Declaration
$moduleID = 95;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\components\ajax\ajaxPage;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Html\HTMLModulePage;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	if (empty($_POST['dirName']))
	{
		$hasError = TRUE;
		$header = $errorNtf->addErrorHeader("err", "Directory Name");
		$errorNtf->addErrorDescription($header, "errDesc", "Directory name cannot be empty.", $extra = "");
	}
	
	if ($hasError)
		return $errorNtf->getReport();
	
	$ajaxPage = new ajaxPage();
	$status = $ajaxPage->create($_POST['pageName'], $_POST['dirName']);
	
	// Return form report
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
	
	// Return form report
	//return simpleForm::getSubmitContent(ServerReporter::statusReport($status, "", FALSE), $reset = TRUE);
}

$frame = new dialogFrame();
$frame->build("Create new Ajax Page", $moduleID, "createPage", FALSE);
$form = new simpleForm();

// Notification header
$header = moduleLiteral::get($moduleID, "hdr_createNewPage");
$frame->append($header);

// Form Inputs

// Parent Directory
$input = $form->getInput($type = "text", $name = "dirName", $value = "", $class = "", $autofocus = TRUE);
$libRow = $form->buildRow("Parent Directory", $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Directory Name
$input = $form->getInput($type = "text", $name = "pageName", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow("Name (.php)", $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>