<?php
//#section#[header]
// Module Declaration
$moduleID = 89;

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
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\tabControl;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\documentation\classDocumentor;
use \INU\Developer\documentation\documentor;
use \INU\Developer\redWIDE;
use \DEV\Core\sdk\sdkObject;


use \API\Developer\resources\documentation\documentor as documentParser; 

// Initialize SDK Object
$sdkObj = new sdkObject($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $_GET['lib']."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create Global Container
$HTMLContentBuilder->build("", "objectGlobalContainer");
$globalContainer = $HTMLContentBuilder->get();

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabber->build($id = "tbr_".$objID);
$objectTabberControl = $objectTabber->get();
DOM::append($globalContainer, $objectTabberControl);

// Create Tabs
//_____ Source Code Tab
$tab_id = $objID."_sourceCode";
$header = moduleLiteral::get($moduleID, "lbl_sourceCode");
$objectSourceCodeContainer = DOM::create("div", "", "", "objectSourceCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceCodeContainer, $selected = TRUE);
DOM::attr($objectSourceCodeContainer, "style", "height:100%;");
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_JSCode");
$objectJSCodeContainer = DOM::create("div", "", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectJSCodeContainer, $selected = FALSE);
DOM::attr($objectJSCodeContainer, "style", "height:100%;");
//_____ Object Model Tab
$tab_id = $objID."_objModel";
$header = moduleLiteral::get($moduleID, "lbl_objectModel");
$objectModelContainer = DOM::create("div", "", "", "objectModel");
$objectTabber->insertTab($tab_id, $header, $objectModelContainer, $selected = FALSE);
DOM::attr($objectModelContainer, "style", "height:100%;");
//_____ Documentation Tab
$tab_id = $objID."_objDocumentor";
$header = moduleLiteral::get($moduleID, "lbl_objDocumentor");
$objDocumentorContainer = DOM::create("div", "", "", "objDocumentor");
$objectTabber->insertTab($tab_id, $header, $objDocumentorContainer, $selected = FALSE);
DOM::attr($objDocumentorContainer, "style", "height:100%;");


// Group Container
$groupContainer = DOM::create("div", "", "groupContainer");
DOM::append($globalContainer, $groupContainer);

// Create Source Code Form
$sourceCode_form = new simpleForm();
$sourceCodeForm = $sourceCode_form->build($moduleID, "sourceCode", $controls = FALSE)->get();
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $sourceCode_form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($libIDElem);
//_____ Package
$pkgIDElem = $sourceCode_form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $sourceCode_form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $sourceCode_form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$sourceCode_form->append($objIDElem);

// Outer Code Container
$class_container = DOM::create();
$sourceCode_form->append($class_container);

$codeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$codeMgrToolbar->build($dock = "T", $class_container);
DOM::append($class_container, $codeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$codeMgrToolbar->insertToolbarItem($saveTool);
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $codeMgrToolbar->insertToolbarItem($deleteTool);
$attr = array();
$attr['lib'] = $_GET['lib'];
$attr['pkg'] = $_GET['pkg'];
$attr['ns'] = $_GET['ns'];
$attr['oid'] = $_GET['oid'];
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteObject", "", $attr);
/*$documentTool = DOM::create("div", "", "", "sideTool document");
$tlb->insert_tool($documentTool);*/

// Create Code Container (With Documentation) in a slider viewer
// Source Code Editor
$editor = new codeEditor();
$code = $sdkObj->getSourceCode();
$class_editor = $editor->build("php", $code)->get();

// Class Documentor Container
$classDocumentor = new classDocumentor($class_editor);
$manual = $sdkObj->getSourceDoc();
$docWrapper = $classDocumentor->build($manual)->get();
DOM::append($class_container, $docWrapper);

//_____ Create Javascript Code Form
$JSCode_form = new simpleForm();
$JSCode_form->build($moduleID, "jsCode", $controls = FALSE);
$JSCodeForm = $JSCode_form->get();
//_____ Append to group
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $JSCode_form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$JSCode_form->append($libIDElem);
//_____ Package
$pkgIDElem = $JSCode_form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$JSCode_form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $JSCode_form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$JSCode_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $JSCode_form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$JSCode_form->append($objIDElem);

// Outer Code Container
$js_container = DOM::create();
$JSCode_form->append($js_container);
//toolbar::setParent($js_container, $dock = "T");

$jsCodeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$jsCodeMgrToolbar->build($dock = "T", $js_container);
DOM::append($js_container, $jsCodeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$jsCodeMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "JSCodeContainer");
DOM::attr($JSCodeContainer, "style", "height:100%;");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$code = $sdkObj->getJSCode();
$js_editor = $editor->build("js", $code)->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$CSSStyle_form = new simpleForm();
$CSSStyle_form->build($moduleID, "objModel", $controls = FALSE);
$CSSStyleForm = $CSSStyle_form->get();
//_____ Append to group
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ Library
$libIDElem = $CSSStyle_form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($libIDElem);
//_____ Package
$pkgIDElem = $CSSStyle_form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $CSSStyle_form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($nsIDElem);
//_____ Object ID
$objIDElem = $CSSStyle_form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$CSSStyle_form->append($objIDElem);

// Outer Code Container
$obj_container = DOM::create();
$CSSStyle_form->append($obj_container);

$objMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$objMgrToolbar->build($dock = "T", $obj_container);
DOM::append($obj_container, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "objModelContainer");
DOM::attr($objModelContainer, "style", "height:100%;");
DOM::append($obj_container, $objModelContainer);


// CSS Model Editor
$code = $sdkObj->getCSSCode();
$structure = $sdkObj->getCSSModel();
$editor = new cssEditor("objectCSS", "objectModel");
$css_editor = $editor->build($structure, $code)->get();
DOM::append($objModelContainer, $css_editor);

//_____ Create Documentation Form
// Create Container 
$docContainer = DOM::create("div", "", "docContainer");
DOM::attr($docContainer, "style", "height:100%;");
DOM::append($objDocumentorContainer, $docContainer);

// Object's Manual Editor
$docPath = $sdkObj->getManual();
$documentParser = new documentParser();
$documentParser->loadFile($docPath, FALSE);
$documentor = new documentor(TRUE, $docPath);

$id = $objID."_documentor";
$documentor->build($id, $documentParser->getDoc());
DOM::append($docContainer, $documentor->get());

// Send redWIDE Tab
$header = $_GET['oid'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($objID, $header, $globalContainer);
//#section_end#
?>