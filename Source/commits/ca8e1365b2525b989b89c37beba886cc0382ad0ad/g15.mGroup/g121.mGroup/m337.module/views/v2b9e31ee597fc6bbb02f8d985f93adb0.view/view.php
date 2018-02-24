<?php
//#section#[header]
// Module Declaration
$moduleID = 337;

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
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Resources\pages\pageFolder;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

// Get current folder id
$currentFolderID = engine::getVar('cfid');

if (engine::isPost())
{
	$has_error = FALSE;

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$success = pageFolder::remove($_POST['fid']);
		
	// If there is an error in creating the folder, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Folder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error deleting folder..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_delFolder");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteFolder");
$form = $frame->getFormFactory();

// Get all folders
$folders = pageFolder::getAllFolders();
foreach ($folders as $folder)
{
	// Normalize folder title
	$folderTitle = ($folder['name'] == "" ? $folder['domain'] : $folder['name']);
	$folderTitle = ($folder['is_root'] ? $folder['domain'] : $folderTitle);
	
	// Get parent title (if any)
	$parentTitle = $folderResource[$folder['parent_id']];
	
	// Add resource
	$folderResource[$folder['id']] = ($parentTitle == "" ? "" : $parentTitle." > ").$folderTitle;
}
asort($folderResource);

// Parent Folder
$title = moduleLiteral::get($moduleID, "lbl_folder");
$input = $form->getResourceSelect($name = "fid", $multiple = FALSE, $class = "", $folderResource, $selectedValue = $currentFolderID);
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

return $frame->getFrame();
//#section_end#
?>