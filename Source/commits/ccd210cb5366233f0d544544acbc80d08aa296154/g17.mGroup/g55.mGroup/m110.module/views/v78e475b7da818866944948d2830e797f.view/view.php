<?php
//#section#[header]
// Module Declaration
$moduleID = 110;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Layout");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Layout\pageLayout;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	if (!simpleForm::validate())
	{
		$hasError = TRUE;
		
		// Header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addErrorHeader("lblDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDesc_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}
	if ($hasError)
		return $errFormNtf->getReport();
	
	// Commit SDK Object
	$layoutManager = new pageLayout($_POST['category'], $_POST['name']);
	$status = $layoutManager->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_deleteLayout");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", DOM::create("span", $status));
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
$frame->build("Delete Layout", $moduleID, "deleteLayout", FALSE);
$sForm = new simpleForm();

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteLayout");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = $_GET['category']."/".$_GET['name'].".layout";
$p = DOM::create("h4", $path);
$frame->append($p);

// Layout Category
$input = $sForm->getInput($type = "hidden", $name = "category", $value = $_GET['category']);
$frame->append($input);

// Layout name
$input = $sForm->getInput($type = "hidden", $name = "name", $value = $_GET['name']);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>