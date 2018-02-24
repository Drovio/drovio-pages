<?php
//#section#[header]
// Module Declaration
$moduleID = 173;

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
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\redWIDE;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$globalContainer = $pageContent->build("", "applicationViewContainer")->get();

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
$viewName = $_GET['name'];
$appView = $devApp->getView($viewName);

// Create object id
$objID = $appID."_view_".$viewName;

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID)->get();
DOM::append($globalContainer, $objectTabberControl);

// Create Tabs
//_____ HTML + CSS Code Tab
$tab_id = $objID."_htmlCode";
$header = moduleLiteral::get($moduleID, "lbl_htmlCode");
$objectHtmlContainer = DOM::create("div", "", "viewHtmlCode");
$objectTabber->insertTab($tab_id, $header, $objectHtmlContainer, $selected = TRUE);
DOM::attr($objectHtmlContainer, "style", "height:100%;");
//_____ PHP Code Tab
$tab_id = $objID."_phpCode";
$header = moduleLiteral::get($moduleID, "lbl_phpCode");
$objectSourceContainer = DOM::create("div", "", "viewPhpCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceContainer, $selected = FALSE);
DOM::attr($objectSourceContainer, "style", "height:100%;");
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_jsCode");
$objectScriptContainer = DOM::create("div", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectScriptContainer, $selected = FALSE);
DOM::attr($objectScriptContainer, "style", "height:100%;");

// Create form object
$form = new simpleForm();

// Designer Form 
$designerForm = $form->build($moduleID, "updateHTML", $controls = FALSE)->get();
DOM::append($objectHtmlContainer, $designerForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer COntainer
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "viewModelContainer");
DOM::attr($objModelContainer, "style", "height:100%;");
DOM::append($outerContainer, $objModelContainer);

// CSS Editor
$html = $appView->getHTML();
$css = trim($appView->getCSS());
$editor = new cssEditor("viewCSS", "viewHTML");
$viewDesigner = $editor->build($html, $css)->get();
DOM::append($objModelContainer, $viewDesigner);


// Create form object
$form = new simpleForm();

// Source Code Form 
$sourceForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceContainer, $sourceForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Container
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$viewSourceContainer = DOM::create("div", "", "viewSourceContainer");
DOM::attr($viewSourceContainer, "style", "height:100%;");
DOM::append($outerContainer, $viewSourceContainer);

// PHP Editor
$editor = new codeEditor();
$code = trim($appView->getPHPCode());
$phpEditor = $editor->build("php", $code, "viewSource")->get();
DOM::append($viewSourceContainer, $phpEditor);


// Create form object
$form = new simpleForm();

// Source Code Form 
$jsForm = $form->build($moduleID, "updateScript", $controls = FALSE)->get();
DOM::append($objectScriptContainer, $jsForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Container
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$viewScriptContainer = DOM::create("div", "", "viewScriptContainer");
DOM::attr($viewScriptContainer, "style", "height:100%;");
DOM::append($outerContainer, $viewScriptContainer);

// Javascript Editor
$editor = new codeEditor();
$code = trim($appView->getJS());
$jsEditor = $editor->build("js", $code, "viewScript")->get();
DOM::append($viewScriptContainer, $jsEditor);

// Send redWIDE Tab
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($objID, $viewName, $globalContainer);
//#section_end#
?>