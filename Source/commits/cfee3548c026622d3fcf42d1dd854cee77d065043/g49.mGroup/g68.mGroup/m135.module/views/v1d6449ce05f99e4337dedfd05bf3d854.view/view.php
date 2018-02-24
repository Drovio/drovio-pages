<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;
use \UI\Presentation\notification;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\redWIDE;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize application
	$appID = $_POST['appID'];
	$devApp = new application($appID);
	
	// Get Application view
	$styleName = $_POST['name'];
	$appStyle = $devApp->getStyle($styleName);
	
	// Get Source Code
	$styleCode = $_POST['styleCode'];
	
	// Update ScriptCode
	$status = $appStyle->update($styleCode);
	
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

// Initialize Application
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}
$devApp = new application($appID);

// Get Application view
$styleName = $_GET['name'];
$appStyle = $devApp->getStyle($styleName);

// Create object id
$objID = $appID."_style_".$styleName;


// Create Global Container
$globalContainer = DOM::create("div", "", "objectGlobalContainer");

// Create Code Form
$form = new simpleForm();
$formElement = $form->build($moduleID, "styleEditor", FALSE)->get();
DOM::append($globalContainer, $formElement);

// Create navigation bar
$tlb = new navigationBar();
$navBar = $tlb->build($dock = navigationBar::TOP, $globalContainer)->get();
DOM::append($formElement, $navBar);

//_____ Save button
$saveObject = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveObject, "type", "submit");
$attr = array();
$attr['app'] = $appName;
$attr['script'] = $scriptName;
$tlb->insert_tool($saveObject);

// Hidden form values
$hidden_appName = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($hidden_appName);

$hidden_scriptName = $form->getInput($type = "hidden", $name = "name", $value = $styleName, $class = "", $autofocus = FALSE);
$form->append($hidden_scriptName);

// Source Code Editor
$editor = new codeEditor();
$styleCode = $appStyle->get();
$scriptEditor = $editor->build("css", $styleCode, "styleCode")->get();
$form->append($scriptEditor);


// Send redWIDE Report Content
$wide = new redWIDE();
return $wide->getReportContent($objID, $styleName.".css", $globalContainer);
//#section_end#
?>