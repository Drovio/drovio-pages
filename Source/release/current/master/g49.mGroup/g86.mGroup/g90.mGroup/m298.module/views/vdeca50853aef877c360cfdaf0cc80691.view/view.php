<?php
//#section#[header]
// Module Declaration
$moduleID = 298;

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
importer::import("DEV", "WebEngine");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \DEV\WebEngine\distroManager;
use \DEV\WebEngine\webCoreProject;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "newDistroContainer");

// Post method
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check distro Name
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_distroName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create distro
	$dman = new distroManager();
	$status = $dman->create($_POST['name']);
	
	// If there is an error in creating the object, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_newDistro");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating distribution package..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build form
$form = new simpleForm();
$newScope = $form->build()->engageModule($moduleID, "createNewDistro")->get();
$pageContent->append($newScope);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Header
$title = moduleLiteral::get($moduleID, "hd_newDistro");
$hdr = DOM::create("h2", $title);
$form->append($hdr);

// Literal Scope
$title = moduleLiteral::get($moduleID, "lbl_distroName");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($inputRow);

// Return output
return $pageContent->getReport();
//#section_end#
?>