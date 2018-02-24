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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\tabControl;
use \INU\Developer\redWIDE;
use \INU\Developer\cssEditor;
use \INU\Developer\codeEditor;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
}



// Initialize Application
$appName = $_GET['app'];
$viewName = $_GET['name'];
$devApp = new application($appName);
$appView = $devApp->getView($viewName);


// Create TabControl for Sections
$viewTabber = new tabControl();
$viewTabberControl = $viewTabber->build($id = "app_viewEditor")->get();

// Designer Tab Page
$designerTabPage = DOM::create("div", "", "", "viewDesigner");
$viewTabber->insertTab("view_design", "Designer", $designerTabPage, $selected = TRUE);

// Script Tab Page
$coderTabPage = DOM::create("div", "");
$viewTabber->insertTab("viewScript", "Coder", $coderTabPage, $selected = FALSE);

// View Designer Editor
$designEditor = new cssEditor("viewCss", "viewDesigner");
$designEditorElement = $designEditor->build($appView->getStructure(), $appView->getCSS())->get();
DOM::append($designerTabPage, $designEditorElement);

// Javascript Code Editor
$editor = new codeEditor();
$scriptObj = $devApp->getView($viewName);
$scriptEditor = $editor->build("js", $appView->getSourceCode())->get();
DOM::append($coderTabPage, $scriptEditor);


// Send redWIDE Report Content
$wide = new redWIDE();
return $wide->getReportContent($appName.$viewName."view", $viewName.".view", $viewTabberControl);
//#section_end#
?>