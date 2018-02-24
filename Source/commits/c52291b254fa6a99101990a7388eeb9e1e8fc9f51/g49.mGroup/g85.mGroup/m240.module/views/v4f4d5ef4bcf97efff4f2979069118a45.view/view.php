<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

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
importer::import("UI", "Modules");
importer::import("INU", "Developer");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Forms\templates\simpleForm;
use \INU\Developer\redWIDE;
use \DEV\Modules\moduleGroup;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize view
	$groupInfoID = $_POST['gid'];
	
	if (isset($_POST['delete']))
		$status = moduleGroup::remove($groupInfoID);
	else
	{
		// Update module title and description
		if (!empty($_POST['title']))
			$status = moduleGroup::update($groupInfoID, $_POST['title']);
		else
			$status = FALSE;
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

// Get group id
$groupInfoID = $_GET['gid'];
$itemID = $groupInfoID."_info";

// Initialize module
$groupInfo = moduleGroup::info($groupInfoID);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("group_".$itemID, "groupInfoEditor", TRUE);


// Title
$header = DOM::create("h3", "Group Info Editor: ".$groupInfoID);
$pageContent->append($header);


// Build form editor
$form = new simpleForm("groupInfoEditorForm_".$groupInfoID);
$editorForm = $form->build($moduleID, "groupInfo", TRUE)->get();
$pageContent->append($editorForm);

$input = $form->getInput($type = "hidden", "gid", $groupInfoID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_groupTitle");
$input = $form->getInput($type = "text", "title", $groupInfo['description'], $class = "", $autofocus = TRUE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($row);

$title = moduleLiteral::get($moduleID, "lbl_delGroup");
$notes = moduleLiteral::get($moduleID, "lbl_delGroup_notes");
$input = $form->getInput($type = "checkbox", "delete", "", $class = "", $autofocus = TRUE, $required = FALSE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes);
$form->append($row);

// Get wide tabber
$header = $groupInfo['description'].":INFO";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>