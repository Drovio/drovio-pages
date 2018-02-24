<?php
//#section#[header]
// Module Declaration
$moduleID = 264;

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
importer::import("DEV", "Apps");
importer::import("DEV", "Documentation");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\tabControl;
use \UI\Developer\codeEditor;
use \UI\Developer\editors\CSSEditor;
use \UI\Developer\devTabber;
use \DEV\Apps\source\srcObject;
use \DEV\Documentation\classDocEditor;

// Initialize SDK Object
$appID = engine::getVar('id');
$sdkObj = new srcObject($appID, $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $appID."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$pageContent->build("", "objectGlobalContainer");

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabber->build($id = "tbr_".$objID);
$objectTabberControl = $objectTabber->get();
$pageContent->append($objectTabberControl);

// Create Tabs
//_____ Source Code Tab
$tab_id = $objID."_sourceCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_sourceCode");
$objectSourceCodeContainer = DOM::create("div", "", "", "objectSourceCode");
$objectTabber->insertTab($tab_id, $header, $objectSourceCodeContainer, $selected = TRUE);
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_jsCode");
$objectJSCodeContainer = DOM::create("div", "", "", "objectJSCode");
$objectTabber->insertTab($tab_id, $header, $objectJSCodeContainer, $selected = FALSE);
//_____ Object Model Tab
$tab_id = $objID."_objModel";
$header = moduleLiteral::get($moduleID, "lbl_tab_objModel");
$objectModelContainer = DOM::create("div", "", "", "objectModel");
$objectTabber->insertTab($tab_id, $header, $objectModelContainer, $selected = FALSE);


// Group Container
$groupContainer = DOM::create("div", "", "groupContainer");
$pageContent->append($groupContainer);

// Create Source Code Form
$form = new simpleForm();
$sourceCodeForm = $form->build("", FALSE)->engageModule($moduleID, "updateSource")->get();
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($input);

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
$attr['id'] = $appID;
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
$classDocumentor = new classDocEditor($class_editor);
$manual = $sdkObj->getSourceDoc();
$docWrapper = $classDocumentor->build($manual)->get();
DOM::append($class_container, $docWrapper);

//_____ Create Javascript Code Form
$form = new simpleForm();
$JSCodeForm = $form->build("", FALSE)->engageModule($moduleID, "updateScript")->get();
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Code Container
$js_container = DOM::create();
$form->append($js_container);

$jsCodeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$jsCodeMgrToolbar->build($dock = "T", $js_container);
DOM::append($js_container, $jsCodeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$jsCodeMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "", "JSCodeContainer");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$code = $sdkObj->getJSCode();
$js_editor = $editor->build("js", $code)->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$form = new simpleForm();
$CSSStyleForm = $form->build("", FALSE)->engageModule($moduleID, "updateModel")->get();
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Code Container
$obj_container = DOM::create();
$form->append($obj_container);

$objMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$objMgrToolbar->build($dock = "T", $obj_container);
DOM::append($obj_container, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "", "objModelContainer");
DOM::append($obj_container, $objModelContainer);


// CSS Model Editor
$code = $sdkObj->getCSSCode();
$structure = $sdkObj->getCSSModel();
$editor = new CSSEditor("objectCSS", "objectModel");
$css_editor = $editor->build($structure, $code)->get();
DOM::append($objModelContainer, $css_editor);

// Send redWIDE Tab
$devTabber = new devTabber();
$obj_id = $_GET['pkg'].($_GET['ns'] == "" ? "" : "_".$_GET['ns'])."_".$_GET['oid'];
$header = $_GET['oid'];
return $devTabber->getReportContent($obj_id, $header, $pageContent->get());
//#section_end#
?>