<?php
//#section#[header]
// Module Declaration
$moduleID = 263;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("DEV", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\source\srcPackage;
use \DEV\Apps\source\srcObject;

$appID = engine::getVar('id');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Create View
	$srcObject = new srcObject($appID, $_POST['package'], $_POST['namespace']);
	$result = $srcObject->create($_POST['name']);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = "ERROR";
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating source object..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Refresh the source list
	$succFormNtf->addReportAction("application.source.explorer.refresh");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_createObjectTitle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createObject");
$form = $frame->getFormFactory();

// Application ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Package Name
$sdkp = new srcPackage($appID);
$packages = $sdkp->getList();
foreach ($packages as $package)
	$packages[$package] = srcPackage::LIB_NAME." > ".$package;
ksort($packages);
$title = moduleLiteral::get($moduleID, "lbl_packageName");
$input = $form->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_parentNs");
$notes = moduleLiteral::get($moduleID, "lbl_parentNs_notes", array(), FALSE);
$input = $form->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = TRUE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);

// Object Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>