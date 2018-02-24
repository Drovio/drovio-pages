<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\windowFrame;
use \UI\Layout\pageLayout;
use \UI\Modules\MContent;

// Create container
$pageContent = new MContent();
$pageContent->build("", "newLayoutDialog");

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Layout Name
	$empty = (is_null($_POST['name']) || empty($_POST['name']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $formErrorNotification->getReport();
	
	//No parametres error -> Continue
	$category = $_POST['category'];
	$name = $_POST['name'];
	
	$layout = new pageLayout($category);
	$success = $layout->create($name);
	
	// If there is an error in creating the object, show it
	if (!$success)
	{
		$err_header = "Error";
		$err = $errFormNtf->addErrorHeader("objName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "objName_desc", DOM::create("span", "Error creating layout..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Frame
$frame = new windowFrame();

// Title
$title = moduleLiteral::get($moduleID, "createLayout", FALSE);
$frame->build($title);

// Create form
$newLayoutFormObject = new simpleForm();
$newLayoutFormElement = $newLayoutFormObject->build($moduleID, "newLayout", $controls = TRUE)->get();
$pageContent->append($newLayoutFormElement);

// Group
$groupOptions = array();
//Selector values
$groupOptions["system"] = moduleLiteral::get($moduleID, "hdr_globalLayouts", FALSE);
$groupOptions["ebuilder"] = moduleLiteral::get($moduleID, "hdr_webLayouts", FALSE);
$title = moduleLiteral::get($moduleID, "lbl_layoutCategory");
$input = $newLayoutFormObject->getResourceSelect($name = "category", $multiple = FALSE, $class = "", $groupOptions, $selectedValue = "");
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Object Name
$title = literal::dictionary("name");
$input = $newLayoutFormObject->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$newLayoutFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Append Container to Frame
return $frame->append($pageContent->get())->getFrame();
//#section_end#
?>