<?php
//#section#[header]
// Module Declaration
$moduleID = 235;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
importer::import("DEV", "Core");
importer::import("DEV", "Documentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\tabControl;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\redWIDE;
use \DEV\Core\sdk\sdkObject;
use \DEV\Documentation\classDocEditor;


// Initialize SDK Object
$sdkObj = new sdkObject($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $_GET['lib']."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$pageContent->build("", "objectGlobalContainer");

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID)->get();
$pageContent->append($objectTabberControl);

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

//_____ Documentation Tab
$tab_id = $objID."_objDocumentor";
$header = moduleLiteral::get($moduleID, "lbl_objDocumentor");
$objDocumentorContainer = DOM::create("div", "", "", "objDocumentor");
$objectTabber->insertTab($tab_id, $header, $objDocumentorContainer, $selected = FALSE);



// Group Container
$groupContainer = DOM::create("div", "", "groupContainer");
$pageContent->append($groupContainer);

// Create Source Code Form
$form = new simpleForm();
$sourceCodeForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$class_container = DOM::create();
$form->append($class_container);

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

// Create Code Container (With Documentation) in a slider viewer
// Source Code Editor
$editor = new codeEditor();
$code = $sdkObj->getSourceCode();
$class_editor = $editor->build("php", $code)->get();

// Class Documentor Container
$classDocEditor = new classDocEditor($class_editor);
$manual = $sdkObj->getSourceDoc();
$docWrapper = $classDocEditor->build($manual)->get();
DOM::append($class_container, $docWrapper);

//_____ Create Javascript Code Form
$form = new simpleForm();
$JSCodeForm = $form->build($moduleID, "updateJs", $controls = FALSE)->get();
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ Library
$libIDElem = $form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$js_container = DOM::create();
$form->append($js_container);

// Create Source Code Manager Toolbar
$jsCodeMgrToolbar = new navigationBar();
$toolbar = $jsCodeMgrToolbar->build($dock = "T", $js_container)->get();
DOM::append($js_container, $toolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$jsCodeMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "JSCodeContainer");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$code = $sdkObj->getJSCode();
$js_editor = $editor->build("js", $code)->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$form = new simpleForm();
$CSSStyleForm = $form->build($moduleID, "updateModel", $controls = FALSE)->get();
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ Library
$libIDElem = $form->getInput("hidden", "libID", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkgID", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "nsID", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "objID", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$obj_container = DOM::create();
$form->append($obj_container);

// Create Source Code Manager Toolbar
$objMgrToolbar = new navigationBar();
$toolbar = $objMgrToolbar->build($dock = "T", $obj_container)->get();
DOM::append($obj_container, $toolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "objModelContainer");
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
DOM::append($objDocumentorContainer, $docContainer);

// Send redWIDE Tab
$header = $_GET['oid'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($objID, $header, $pageContent->get());
//#section_end#
?>