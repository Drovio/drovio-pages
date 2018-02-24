<?php
//#section#[header]
// Module Declaration
$moduleID = 266;

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
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\views\appViewManager;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
		
	// Remove folder
	$vMan = new appViewManager($_POST['appID']);
	$result = $vMan->removeFolder($_POST['parent']);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error removing view folder..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_deleteViewFolderTitle");
$frame->build($title, $moduleID, "deleteViewFolder", FALSE);
$form = new simpleForm();

// Validate and Load application data
$appID = $_GET['appID'];

// Application ID
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Folder Parent
$folderResources = array();
$vman = new appViewManager($appID);
$viewFolders = $vman->getFolders("", TRUE);
foreach ($viewFolders as $fl)
	$folderResources[$fl] = $fl;
ksort($folderResources);

// Folder to delete
$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>