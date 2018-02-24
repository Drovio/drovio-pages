<?php
//#section#[header]
// Module Declaration
$moduleID = 134;

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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Developer\components\ebuilder\ebObject;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Presentation\tabControl;
use \UI\Navigation\navigationBar;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\documentor;


// Initialize eBuilder Object
$ebObj = new ebObject($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $_GET['lib']."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Toolbar Control
$tlb = new navigationBar();

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create Global Container
$HTMLContentBuilder->build("", "objectGlobalContainer");
$globalContainer = $HTMLContentBuilder->get();

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID)->get();
DOM::append($globalContainer, $objectTabberControl);

// Create Tabs
//_____ Source Code Tab
$tab_id = $objID."_sourceCode";
$header = moduleLiteral::get($moduleID, "lbl_sourceCode");
$objectSourceCodeContainer = DOM::create("div", "", "", "objectSourceCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceCodeContainer, $selected = TRUE);
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_JSCode");
$objectJSCodeContainer = DOM::create("div", "", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectJSCodeContainer, $selected = FALSE);
//_____ Object Model Tab
$tab_id = $objID."_objModel";
$header = moduleLiteral::get($moduleID, "lbl_objectModel");
$objectModelContainer = DOM::create("div", "", "", "objectModel");
$objectTabber->insertTab($tab_id, $header, $objectModelContainer, $selected = FALSE);

// Group Container
$groupContainer = DOM::create("div", "", "", "groupContainer");
DOM::append($globalContainer, $groupContainer);

//_____ Create Source Code Form
$sourceCode_form = new simpleForm();
$sourceCodeForm = $sourceCode_form->build($moduleID, "sourceCode", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $sourceCode_form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($libIDElem);
//_____ Package
$pkgIDElem = $sourceCode_form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($pkgIDElem);

//_____ Namespace
$nsIDElem = $sourceCode_form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $sourceCode_form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($objIDElem);

// Outer Code Container
$class_container = DOM::create();
$sourceCode_form->append($class_container);
navigationBar::setParent($class_container, $dock = "T");

// Create Source Code Manager Toolbar
$codeMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($class_container, $codeMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertTool($saveTool);


// Create Code Container (With Documentation) in a slider viewer
// Source Code Editor
$editor = new codeEditor();
$code = $ebObj->getSourceCode();
$class_editor = $editor->build("php", $code)->get();

// Class Documentor Container //
$documentor = new documentor($class_editor);
$manual = $ebObj->getSourceDoc();
$docWrapper = $documentor->build($manual)->get();
DOM::append($class_container, $docWrapper);


//_____ Create Javascript Code Form
$JSCode_form = new simpleForm();
$JSCodeForm = $JSCode_form->build($moduleID, "jsCode", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $JSCode_form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$JSCode_form->append($libIDElem);
//_____ Package
$pkgIDElem = $JSCode_form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$JSCode_form->append($pkgIDElem);

//_____ Namespace
$nsIDElem = $JSCode_form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$JSCode_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $JSCode_form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$JSCode_form->append($objIDElem);


// Outer Code Container
$js_container = DOM::create();
$JSCode_form->append($js_container);
navigationBar::setParent($js_container, $dock = "T");

// Create Source Code Manager Toolbar
$jsCodeMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($js_container, $jsCodeMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertTool($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "", "JSCodeContainer");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$code = $ebObj->getJSCode();
$js_editor = $editor->build("js", $code)->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$CSSStyle_form = new simpleForm();
$CSSStyleForm = $CSSStyle_form->build($moduleID, "objModel", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ Library
$libIDElem = $CSSStyle_form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($libIDElem);
//_____ Package
$pkgIDElem = $CSSStyle_form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($pkgIDElem);

//_____ Namespace
$nsIDElem = $CSSStyle_form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $CSSStyle_form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($objIDElem);


// Outer Code Container
$obj_container = DOM::create();
$CSSStyle_form->append($obj_container);
navigationBar::setParent($obj_container, $dock = "T");

// Create Source Code Manager Toolbar
$objMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($obj_container, $objMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertTool($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "", "objModelContainer");
DOM::append($obj_container, $objModelContainer);


// Source Code Editor
$code = $ebObj->getCSSCode();
$structure = $ebObj->getCSSModel();
$editor = new cssEditor("objectCSS", "objectModel");
$css_editor = $editor->build($structure, $code)->get();
DOM::append($objModelContainer, $css_editor);


// Send redWIDE Tab
$obj_id = $_GET['pkg'].($_GET['ns'] == "" ? "" : "_".$_GET['ns'])."_".$_GET['oid'];
$header = $_GET['oid'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $globalContainer);
//#section_end#
?>