<?php
//#section#[header]
// Module Declaration
$moduleID = 249;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\domain;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
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
		$dbc = new dbConnection;
		
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
	
	$success = domain::create($_POST['title'], $_POST['path']);
	
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
$title = moduleLiteral::get($moduleID, "hd_createDomain");
$frame->build($title, $moduleID, "createDomain", FALSE);
$sForm = new simpleForm();

// Domain Title
$title = literal::dictionary("name");
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

// Return the report
return $frame->getFrame();
//#section_end#
?>