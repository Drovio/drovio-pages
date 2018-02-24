<?php
//#section#[header]
// Module Declaration
$moduleID = 285;

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
use \DEV\Websites\pages\wsPageManager;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;

$websiteID = engine::getVar("id");
if (engine::isPost())
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check name
	$empty = (is_null($_POST['name']) || empty($_POST['name']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_name");
		$err_header = $formErrorNotification->addErrorHeader("nameErrorHeader", $header);
		$errFormNtf->addErrorDescription($err_header, "libName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $formErrorNotification->getReport();
	
	// Create folder
	$pMan = new wsPageManager($websiteID);
	$parent = ($_POST['parent'] == -1 ? "" : $_POST['parent']);
	$success = $pMan->createFolder($parent, $_POST['name']);
	
	// If error, show notification
	if (!$success)
	{
		// Header
		$header = "";
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, "Error", $extra = "Error creating folder.");
		return $formErrorNotification->getReport();
	}
	
	// SUCCESS NOTIFICATION
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Description
	$message= $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->appendCustomMessage($message);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_newFolder");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createFolder");
$form = $frame->getFormFactory();

// Folder Parent
$folderResources = array();
$folderResources["-1"] = "/";
$pman = new wsPageManager($websiteID);
$pageFolders = $pman->getFolders("", TRUE);
foreach ($pageFolders as $fl)
	$folderResources[$fl] = $fl;
ksort($folderResources);

$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = TRUE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_folderName");
$label = $form->getLabel($title);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return the report
return $frame->getFrame();
//#section_end#
?>