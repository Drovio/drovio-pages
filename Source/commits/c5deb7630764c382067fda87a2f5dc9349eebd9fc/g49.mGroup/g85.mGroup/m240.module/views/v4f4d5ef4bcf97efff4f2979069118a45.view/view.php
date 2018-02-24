<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

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
importer::import("DEV", "Modules");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\mGroup;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \UI\Forms\templates\simpleForm;
use \UI\Developer\devTabber;
use \DEV\Modules\moduleGroup;
use \DEV\Modules\modulesProject;

if (engine::isPost())
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
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else
	{
		$reportNtf->build($type = notification::ERROR, $header = TRUE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

// Get group id
$groupInfoID = $_GET['gid'];
$itemID = $groupInfoID."_info";

// Initialize module
$groupInfo = mGroup::info($groupInfoID);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("group_".$itemID, "groupInfoEditor", TRUE);


// Title
$header = DOM::create("h3", "Group Info Editor: ".$groupInfoID);
$pageContent->append($header);


// Build form editor
$form = new simpleForm("groupInfoEditorForm_".$groupInfoID);
$editorForm = $form->build()->engageModule($moduleID, "groupInfo", TRUE)->get();
$pageContent->append($editorForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Group ID
$input = $form->getInput($type = "hidden", $name = "gid", $value = $groupInfoID, $class = "", $autofocus = FALSE, $required = FALSE);
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
$WIDETab = new devTabber();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>