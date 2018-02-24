<?php
//#section#[header]
// Module Declaration
$moduleID = 68;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\pageFolder;
use \API\Model\units\sql\dbQuery;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$success = pageFolder::remove($_POST['folderId']);
		
	// If there is an error in creating the folder, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Delete Page Page");
		$err = $errFormNtf->addErrorHeader("lblDelete_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDelete_desc", DOM::create("span", "Error deleting page..."));
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
$frame->build("Delete Folder", $moduleID, "deleteFolder", FALSE);
$sForm = new simpleForm();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_delFolder");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$frame->append($hdr);

// Get all folders
$dbc = new dbConnection();
$dbq = new dbQuery("737200095", "units.domains.folders");
$folders = $dbc->execute($dbq);
	
// Create domain tree on the sidebar
$folderResource = array();
while ($folder = $dbc->fetch($folders))
{
	$folderTitle = ($folder['name'] == "" ? $folder['domain'] : $folder['name']);
	$parentTitle = $folderResource[$folder['parent_id']];
	$folderID = $folder['id'];
	$folderResource[$folderID] = ($parentTitle == "" ? "" : $parentTitle." > ").$folderTitle;
}

// Parent Folder
$title = moduleLiteral::get($moduleID, "lbl_folder");
$input = $sForm->getResourceSelect($name = "folderId", $multiple = FALSE, $class = "", $folderResource, $selectedValue = $pageData['folder_id']);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($libRow);

return $frame->getFrame();
//#section_end#
?>