<?php
//#section#[header]
// Module Declaration
$moduleID = 337;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Resources\pages\domain;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if (engine::isPost())
{
	$has_error = FALSE;
	$exists = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Title
	if (empty($_POST['title']))
	{
		// Get domain info (to check if already exists)
		$info = domain::info($_POST['title']);
		if (!empty($info))
			$exists = TRUE;
	}
	
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	else if ($exists)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.exists"));
	}
	
	// Check Path
	if (empty($_POST['path']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$success = domain::create($_POST['title'], $_POST['path']);
	
	// If there is an error in creating the library, show it
	if (!$success)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_domain");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating domain..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_createDomain");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createDomain");
$form = $frame->getFormFactory();

// Domain Title
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Domain Path
$titleContent = moduleLiteral::get($moduleID, "lbl_folder");
$titleNote = DOM::create("span", " (/_sbd_)");
$title = DOM::create("span");
DOM::append($title, $titleContent);
DOM::append($title, $titleNote);
$input = $form->getInput($type = "text", $name = "path", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>