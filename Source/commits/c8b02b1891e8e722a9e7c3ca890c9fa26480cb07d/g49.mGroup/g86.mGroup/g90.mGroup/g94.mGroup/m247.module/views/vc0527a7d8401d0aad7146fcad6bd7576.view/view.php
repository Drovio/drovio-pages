<?php
//#section#[header]
// Module Declaration
$moduleID = 247;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\WebEngine\sdk\webLibrary;
use \DEV\WebEngine\sdk\webPackage;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();

	// Check Package Name
	$empty = inputValidator::checkNotset($_POST['package']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err = $errFormNtf->addErrorHeader("pkg_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkg_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Namespace Name
	$empty = inputValidator::checkNotset($_POST['nsName']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_namespaceName");
		$err = $errFormNtf->addErrorHeader("objName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "objName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Get libName and packageName
	$packageNameArray = explode("::", $_POST['package']);
	$libName = $packageNameArray[0];
	$packageName = $packageNameArray[1];

	$ebPckg = new webPackage();
	$result = $ebPckg->createNS($libName, $packageName, $_POST['nsName'], $_POST['parentNs']);
	
	// If there is an error in creating the namespace, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_namespaceName");
		$err = $errFormNtf->addErrorHeader("nsName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "nsName_desc", DOM::create("span", "Error creating namespace..."));
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
$title = moduleLiteral::get($moduleID, "hd_createNamespace");
$frame->build($title, $moduleID, "createNamespace", FALSE);
$sForm = new simpleForm();

// Library Name
$ebLib = new webLibrary();
$libraries = $ebLib->getList();
$packages = array();
foreach ($libraries as $library)
{
	$libPackages = $ebLib->getPackageList($library);
	foreach ($libPackages as $package)
		$packages[$library."::".$package] = $library." > ".$package;
}
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $sForm->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Parent Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_parentNamespace");
$input = $sForm->getInput($type = "text", $name = "parentNs", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $sForm->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($nsRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_namespaceName");
$input = $sForm->getInput($type = "text", $name = "nsName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($objRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>