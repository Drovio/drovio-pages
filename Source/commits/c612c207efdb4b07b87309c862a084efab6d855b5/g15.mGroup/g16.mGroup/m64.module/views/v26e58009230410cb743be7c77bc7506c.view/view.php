<?php
//#section#[header]
// Module Declaration
$moduleID = 64;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\units\modules\moduleGroup;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\notification;
use \UI\Forms\templates\simpleForm;
use \INU\Developer\redWIDE;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize view
	$groupInfoID = $_POST['gid'];
	
	// Update module title and description
	if (!empty($_POST['title']))
		$status = moduleGroup::update($groupInfoID, $_POST['title']);
	else
		$status = FALSE;
	
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
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("group_".$itemID, "groupInfoEditor", TRUE);


// Title
$header = DOM::create("h3", "Group Info Editor: ".$groupInfoID);
$pageContent->append($header);


// Build form editor
$form = new simpleForm("groupInfoEditorForm");
$editorForm = $form->build($moduleID, "groupInfo", TRUE)->get();
$pageContent->append($editorForm);

$input = $form->getInput($type = "hidden", "gid", $groupInfoID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_groupTitle");
$input = $form->getInput($type = "text", "title", $groupInfo['description'], $class = "", $autofocus = TRUE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($row);

// Get wide tabber
$header = $groupInfo['description'].":INFO";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>