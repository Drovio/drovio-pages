<?php
//#section#[header]
// Module Declaration
$moduleID = 56;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\frames\windowFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Package Name
	$empty = (is_null($_POST['package']) || empty($_POST['package']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_package");
		$err = $errFormNtf->addErrorHeader("pkgName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "pkgName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Namespace Name
	$empty = (is_null($_POST['nsName']) || empty($_POST['nsName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
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

	$sdk = new sdkPackage();
	$result = $sdk->createNS($libName, $packageName, $_POST['nsName'], $_POST['parentNs']);
	
	// If there is an error in creating the namespace, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_objectName");
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
$frame->build("Create new Namespace", $moduleID, "createNamespace", FALSE);

$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "hd_createNamespace");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

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
$input = $sForm->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Parent Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $sForm->getInput($type = "text", $name = "parentNs", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $sForm->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($nsRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_objectName");
$input = $sForm->getInput($type = "text", $name = "nsName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($objRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>