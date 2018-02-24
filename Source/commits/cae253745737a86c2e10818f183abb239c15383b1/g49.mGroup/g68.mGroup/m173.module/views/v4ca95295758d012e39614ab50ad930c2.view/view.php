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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\redWIDE;
use \DEV\Apps\components\appView;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$globalContainer = $pageContent->build("", "applicationViewContainer")->get();

// Initialize Application
$appID = $_GET['appID'];
$viewName = $_GET['name'];
$appView = new appView($appID, $viewName);

// Create object id
$objID = $appID."_view_".$viewName;

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID)->get();
DOM::append($globalContainer, $objectTabberControl);

// Create Tabs
//_____ PHP Code Tab
$tab_id = $objID."_phpCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_sourceCode");
$objectSourceContainer = DOM::create("div", "", "", "viewPhpCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceContainer, $selected = TRUE);
//_____ HTML + CSS Code Tab
$tab_id = $objID."_htmlCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_designer");
$objectHtmlContainer = DOM::create("div", "", "", "viewHtmlCode");
$objectTabber->insertTab($tab_id, $header, $objectHtmlContainer, $selected = FALSE);
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_behavior");
$objectScriptContainer = DOM::create("div", "", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectScriptContainer, $selected = FALSE);

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