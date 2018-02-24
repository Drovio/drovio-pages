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
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Websites\pages\wsPageManager;
use \UI\Presentation\frames\dialogFrame;

$websiteID = engine::getVar("id");
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
		
	// Remove folder
	$pMan = new wsPageManager($_POST['id']);
	$result = $pMan->removeFolder($_POST['parent']);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error removing view folder..."));
		return $errFormNtf->getReport();
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
$title = moduleLiteral::get($moduleID, "hd_deleteFolder");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteFolder");
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

unset($folderResources["-1"]);
$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return the report
return $frame->getFrame();
//#section_end#
?>