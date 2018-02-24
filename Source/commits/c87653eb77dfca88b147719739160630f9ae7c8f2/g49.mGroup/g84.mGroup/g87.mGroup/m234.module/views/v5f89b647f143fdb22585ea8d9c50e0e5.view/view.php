<?php
//#section#[header]
// Module Declaration
$moduleID = 234;

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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\frames\windowFrame;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\sdk\sdkObject;
use \DEV\Core\coreProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add form post error header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalidate"));
		
		return $errFormNtf->getReport();
	}
	
	// Check Package Name
	if (empty($_POST['package']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Object Name
	if (empty($_POST['objectName']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Get libName and packageName
	$packageNameArray = explode("::", $_POST['package']);
	$libName = $packageNameArray[0];
	$packageName = $packageNameArray[1];

	$sdkObject = new sdkObject($libName, $packageName, $_POST['namespace']);
	$result = $sdkObject->create($_POST['objectName'], $_POST['objectDesc']);
	
	// If there is an error in creating the object, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating object..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_createObject", array(), FALSE);
$frame->build($title, "", FALSE)->engageModule($moduleID, "createObject");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
$packages = array();
foreach ($libraries as $library)
{
	$libPackages = $sdkLib->getPackageList($library);
	foreach ($libPackages as $package)
		$packages[$library."::".$package] = $library." > ".$package;
}
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $form->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $form->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($nsRow);

// Object Name
$title = moduleLiteral::get($moduleID, "lbl_objectName");
$input = $form->getInput($type = "text", $name = "objectName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($objRow);

// Object Description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "objectDesc", $value = "", $class = "");
$objDescRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($objDescRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>