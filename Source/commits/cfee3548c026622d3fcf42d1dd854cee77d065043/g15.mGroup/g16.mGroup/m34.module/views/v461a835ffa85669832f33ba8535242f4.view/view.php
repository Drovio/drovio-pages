<?php
//#section#[header]
// Module Declaration
$moduleID = 34;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\moduleObject;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\sidebar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Presentation\popups\popup;
use \UI\Presentation\notification;
use \INU\Developer\codeEditor;
use \INU\Developer\redWIDE;

if ($_SERVER['REQUEST_METHOD'] == "GET")
	$module_id = $_GET['id'];
else
	$module_id = $_POST['moduleId'];


$moduleObject = new moduleObject($module_id);

// Get Module | Auxiliary
$module = $moduleObject->getModule();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$status = $module->updateJSCode($_POST["moduleJS"]);

	// Build popup
	$reportPopup = new popup();
	$reportPopup->timeout(TRUE);
	$reportPopup->fade(TRUE);
	$reportPopup->position('top');
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// TEMP
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
	else if ($status === FALSE)
	{
		// TEMP
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
		$reportPopup->timeout(FALSE);
	}
	else
	{
		$message = "err.save_error";
		$reportNtf->build($type = "warning", $header = TRUE, $footer = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return redWIDE::getNotificationResult($notification, ($status === TRUE));
}

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Initialize Editor Form
$sForm = new simpleForm();
//$form_builder = new simpleForm();
$sForm->build($moduleID, "moduleJSEditor", $controls = FALSE);
//$submit_editorArea_form = $form_builder->create_form($id = "", "", "editor", $controls = FALSE);
// Append form to Content
$submit_editorArea_form = $sForm->get();
$HTMLContentBuilder->buildElement($submit_editorArea_form);

// Get Module Data from Database
$attr = array();
$attr['plc'] = $module_id;

// Module Attributes
$module_attr = array();
$module_attr['id'] = $module_id;
$module_attr['title'] = $module->getTitle().":Script";
$module_attr['ref'] = "m_".$module_id."_script";
DOM::data($submit_editorArea_form, "module", $module_attr);

// Hidden Values
// Module Id
$input = $sForm->getInput("hidden", "moduleId", $module_id, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Editor Area
$form_content_wrapper = DOM::create("div", "", "", "editorAreaFormContent");
$sForm->append($form_content_wrapper);


// Editor Container
$editorContainer = DOM::create("div", "", "", "editorContainer");
DOM::append($form_content_wrapper, $editorContainer);
// Load Module Code
// Initialize PHP Editor
$coder = new codeEditor();
$content = $module->getJSCode();
$editor = $coder->build($type = "js", $content, "moduleJS")->get();
DOM::append($editorContainer, $editor);

// ##Toolbar
// Toolbar Control 
$tlb = new sidebar();
$tlbItemBuilder = new toolbarItem();
// Create Source Code Manager Toolbar;
$sideToolArea = $tlb->build($dock = "L", $form_content_wrapper)->get();
DOM::append($form_content_wrapper, $sideToolArea); 

//_____ Code Group Tools
$codeGroup = DOM::create("div", "", "", "toolGroup codeGroup");
DOM::append($sideToolArea, $codeGroup);

// Save
$content = DOM::create("button", "",  "saveModule_".$module_attr['ref'], "sideTool save saveModule");
DOM::attr($content, "type", "submit");
$saveTool = $tlbItemBuilder->build($content)->get();
DOM::append($codeGroup, $saveTool); 

$header = $module_attr['title'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($module_attr['ref']."_tab", $header, $HTMLContentBuilder->get());
//#section_end#
?>