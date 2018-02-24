<?php
//#section#[header]
// Module Declaration
$moduleID = 65;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\notification;
use \UI\Forms\templates\simpleForm;
use \INU\Developer\redWIDE;
use \DEV\Modules\module;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize view
	$mID = $_POST['mid'];
	
	$moduleObject = new module($mID);
	
	// Get old module title
	$moduleTitle_old = $moduleObject->getTitle();
	
	if (isset($_POST['delete']))
		$status = $moduleObject->remove();
	else
	{
		// Update module title and description
		if (!empty($_POST['title']))
			$status = $moduleObject->updateInfo($_POST['title'], $_POST['description']);
		else
			$status = FALSE;
		
		// If module updated with success, find and rename view with same name
		if ($status && $moduleTitle_old != $_POST['title'])
		{
			// Get view id (if any)
			$views = $moduleObject->getViews();
			$viewID = array_search($moduleTitle_old, $views);
			if ($viewID)
				$moduleObject->updateViewName($viewID, $_POST['title']);
		}
	}
	
	// Build Notification
	$reportNtf = new notification();
	if ($status == TRUE)
	{
		// TEMP
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
	else
	{
		// TEMP
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return redWIDE::getNotificationResult($notification, ($status === TRUE));
}




// Get module and view ids
$moduleInfoID = $_GET['mid'];
$itemID = $moduleInfoID."_info";

// Initialize module
$moduleObject = new module($moduleInfoID);
$moduleTitle = $moduleObject->getTitle();
$moduleDesc = $moduleObject->getDescription();

// Initialize content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("module_".$itemID, "moduleInfoEditor", TRUE);


// Title
$header = DOM::create("h3", "Module Info Editor: ".$moduleInfoID);
$pageContent->append($header);


// Build form editor
$form = new simpleForm("moduleInfoEditorForm_".$moduleInfoID);
$editorForm = $form->build($moduleID, "moduleInfo", TRUE)->get();
$pageContent->append($editorForm);

$input = $form->getInput($type = "hidden", "mid", $moduleInfoID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_moduleTitle");
$input = $form->getInput($type = "text", "title", $moduleTitle, $class = "", $autofocus = TRUE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($row);

$title = moduleLiteral::get($moduleID, "lbl_moduleDesc");
$input = $form->getTextarea($name = "description", $moduleDesc, $class = "", $autofocus = FALSE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$form->append($row);

$title = moduleLiteral::get($moduleID, "lbl_delModule");
$notes = moduleLiteral::get($moduleID, "lbl_delModule_notes");
$input = $form->getInput($type = "checkbox", "delete", "", $class = "", $autofocus = TRUE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes);
$form->append($row);

// Get wide tabber
$header = $moduleTitle.":INFO";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>