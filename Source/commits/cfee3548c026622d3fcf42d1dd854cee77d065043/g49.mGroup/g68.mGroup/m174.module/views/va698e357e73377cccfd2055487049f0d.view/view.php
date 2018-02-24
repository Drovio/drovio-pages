<?php
//#section#[header]
// Module Declaration
$moduleID = 174;

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
$pageContent->build("", "applicationViewContainer");

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

$objectName = $_GET['name'];
$packageName = $_GET['package'];
$namespace = $_GET['namespace'];
$appSrcObject = $devApp->getSrcObject($packageName, $namespace, $objectName);

// Create object id
$objID = $appID."_object_".$packageName."_".str_replace("::", "_", $namespace)."_".$objectName;

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabber->build($id = "tbr_".$objID);
$objectTabberControl = $objectTabber->get();
$pageContent->append($objectTabberControl);

// Create Tabs
//_____ PHP Code Tab
$tab_id = $objID."_phpCode";
$header = moduleLiteral::get($moduleID, "lbl_sourceCode");
$objectSourceContainer = DOM::create("div", "", "viewPhpCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceContainer, $selected = TRUE);
DOM::attr($objectSourceContainer, "style", "height:100%;");
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_jsCode");
$objectScriptContainer = DOM::create("div", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectScriptContainer, $selected = FALSE);
DOM::attr($objectScriptContainer, "style", "height:100%;");
//_____ Css Code Tab
$tab_id = $objID."_CSSCode";
$header = moduleLiteral::get($moduleID, "lbl_cssCode");
$objectCssContainer = DOM::create("div", "", "viewCssCode");
$objectTabber->insertTab($tab_id, $header, $objectCssContainer, $selected = FALSE);
DOM::attr($objectCssContainer, "style", "height:100%;");

// Create form object
$form = new simpleForm();

// Source Code Form 
$sourceForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceContainer, $sourceForm);

// Hidden Values
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "package", $packageName, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "namespace", $namespace, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "name", $objectName, $class = "", $autofocus = FALSE);
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
$code = trim($appSrcObject->getSourceCode());
$phpEditor = $editor->build("php", $code, "sourceCode")->get();
DOM::append($viewSourceContainer, $phpEditor);


// Create form object
$form = new simpleForm();

// Source Code Form 
$jsForm = $form->build($moduleID, "updateScript", $controls = FALSE)->get();
DOM::append($objectScriptContainer, $jsForm);

// Hidden Values
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "package", $packageName, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "namespace", $namespace, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "name", $objectName, $class = "", $autofocus = FALSE);
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
$code = trim($appSrcObject->getJSCode());
$jsEditor = $editor->build("js", $code, "scriptCode")->get();
DOM::append($viewScriptContainer, $jsEditor);


// Create form object
$form = new simpleForm();

// Designer Form 
$designerForm = $form->build($moduleID, "updateModel", $controls = FALSE)->get();
DOM::append($objectCssContainer, $designerForm);

// Hidden Values
$input = $form->getInput("hidden", "appID", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "package", $packageName, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "namespace", $namespace, $class = "", $autofocus = FALSE);
$form->append($input);
$input = $form->getInput("hidden", "name", $objectName, $class = "", $autofocus = FALSE);
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
$html = $appSrcObject->getCSSModel();
$css = trim($appSrcObject->getCSSCode());
$editor = new cssEditor("cssCode", "modelCode");
$viewDesigner = $editor->build($html, $css)->get();
DOM::append($objModelContainer, $viewDesigner);


// Send redWIDE Tab
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($objID, $objectName, $objectTabberControl);
//#section_end#
?>