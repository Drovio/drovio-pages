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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Html\HTMLModulePage;
use \DEV\Core\ajax\ajaxDirectory;


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
	
	$ajaxDir = new ajaxDirectory();
	$status = $ajaxDir->create($_POST['dirName'], $_POST['parentDir']);
	
	// Return form report
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$frame = new dialogFrame();
$frame->build("Create new Ajax Directory", $moduleID, "createDirectory", FALSE);
$form = new simpleForm();

// Frame header
$header = moduleLiteral::get($moduleID, "hdr_createNewDirectory");
$frame->append($header);

// Form Inputs

// Parent Directory
$input = $form->getInput($type = "text", $name = "parentDir", $value = "", $class = "", $autofocus = TRUE);
$libRow = $form->buildRow("Parent Directory", $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Directory Name
$input = $form->getInput($type = "text", $name = "dirName", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow("Name", $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>