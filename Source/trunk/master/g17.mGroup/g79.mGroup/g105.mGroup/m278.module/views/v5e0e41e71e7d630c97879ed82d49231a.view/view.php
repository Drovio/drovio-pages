<?php
//#section#[header]
// Module Declaration
$moduleID = 278;

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
importer::import("DEV", "Websites");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\frames\windowFrame;
use \DEV\Websites\source\srcLibrary;
use \DEV\Websites\source\srcPackage;

$websiteID = engine::getVar("id");
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Package Name
	if (empty($_POST['package']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Namespace Name
	if (empty($_POST['nsName']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_namespace");
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

	$sdk = new srcPackage($websiteID);
	$result = $sdk->createNS($libName, $packageName, $_POST['nsName'], $_POST['parentNs']);
	
	// If there is an error in creating the namespace, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_namespace");
		$err = $errFormNtf->addErrorHeader("nsName_h", $err_header);
		
		$errFormNtf->addErrorDescription($err, "nsName_desc", DOM::create("span", "Error creating namespace..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_newNamespace");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createNamespace");
$form = $frame->getFormFactory();

// Website id
$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$srcLib = new srcLibrary($websiteID);
$libraries = $srcLib->getList();
$packages = array();
foreach ($libraries as $library)
{
	$libPackages = $srcLib->getPackageList($library);
	foreach ($libPackages as $package)
		$packages[$library."::".$package] = $library." > ".$package;
}
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $form->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($libRow);

// Parent Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_parentNamespace");
$input = $form->getInput($type = "text", $name = "parentNs", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$form->append($nsRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $form->getInput($type = "text", $name = "nsName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($objRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>