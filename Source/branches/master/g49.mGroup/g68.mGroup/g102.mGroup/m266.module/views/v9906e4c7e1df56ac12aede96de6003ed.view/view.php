<?php
//#section#[header]
// Module Declaration
$moduleID = 266;

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
use \DEV\Apps\views\appViewManager;

$appID = engine::getVar('id');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
		
	// Remove folder
	$vMan = new appViewManager($appID);
	$result = $vMan->removeFolder($_POST['parent']);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error removing view folder..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Refresh the views list
	$succFormNtf->addReportAction("application.views.explorer.refresh");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_deleteViewFolderTitle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteViewFolder");
$form = $frame->getFormFactory();

// Application ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = TRUE);
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