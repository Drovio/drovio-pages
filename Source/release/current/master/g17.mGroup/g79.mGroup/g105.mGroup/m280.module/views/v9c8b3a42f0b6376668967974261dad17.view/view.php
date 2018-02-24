<?php
//#section#[header]
// Module Declaration
$moduleID = 280;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("DEV", "Documentation");
importer::import("DEV", "Websites");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Developer\devTabber;
use \UI\Developer\codeMirror;
use \UI\Developer\editors\WViewEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\tabControl;
use \DEV\Websites\source\srcObject;
use \DEV\Documentation\classDocEditor;

// Initialize Source Object
$websiteID = $_GET['id'];
$obj_libName = $_GET['lib'];
$obj_packageName = $_GET['pkg'];
$obj_nsName = $_GET['ns'];
$obj_objectName = $_GET['oid'];
$sdkObj = new srcObject($websiteID, $obj_libName, $obj_packageName, $obj_nsName, $obj_objectName);
// Set Object ID
$objID = $websiteID."_".$obj_libName."_".$obj_packageName."_".($obj_nsName == "" ? "" : $obj_nsName."_").$obj_objectName;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$pageContent->build("", "objectGlobalContainer");

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabber->build($id = "tbr_".$objID, $full = FALSE, $withBorder = FALSE);
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
$sourceCodeForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$input = $form->getInput($type = "hidden", $name = "lib", $obj_libName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput($type = "hidden", $name = "pkg", $obj_packageName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput($type = "hidden", $name = "ns", $obj_nsName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput($type = "hidden", $name = "oid", $obj_objectName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Code Container
$class_container = DOM::create();
$form->append($class_container);

$codeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$codeMgrToolbar->build($dock = navigationBar::TOP, $class_container);
DOM::append($class_container, $codeMgrToolbar->get());

// Delete Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$codeMgrToolbar->insertToolbarItem($saveTool);
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $codeMgrToolbar->insertToolbarItem($deleteTool);
$attr = array();
$attr['id'] = $websiteID;
$attr['wid'] = $websiteID;
$attr['lib'] = $obj_libName;
$attr['pkg'] = $obj_packageName;
$attr['ns'] = $obj_nsName;
$attr['oid'] = $obj_objectName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteObject", "", $attr);

// Create Code Container (With Documentation) in a slider viewer
// Source Code Editor
$editor = new codeMirror($type = codeMirror::PHP, $name = "sourceCode");
$class_editor = $editor->build($sdkObj->getSourceCode(), "", "cmEditor")->get();

// Class Documentor Container
$classDocumentor = new classDocEditor($class_editor);
$manual = $sdkObj->getSourceDoc();
$docWrapper = $classDocumentor->build($manual)->get();
DOM::append($class_container, $docWrapper);

//_____ Create Javascript Code Form
$form = new simpleForm();
$JSCodeForm = $form->build($moduleID, "updateScript", $controls = FALSE)->get();
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$input = $form->getInput($type = "hidden", $name = "lib", $obj_libName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput($type = "hidden", $name = "pkg", $obj_packageName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput($type = "hidden", $name = "ns", $obj_nsName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput($type = "hidden", $name = "oid", $obj_objectName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Code Container
$js_container = DOM::create();
$form->append($js_container);

$jsCodeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$jsCodeMgrToolbar->build($dock = navigationBar::TOP, $js_container);
DOM::append($js_container, $jsCodeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$jsCodeMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "", "JSCodeContainer");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$editor = new codeMirror($type = codeMirror::JS, $name = "jsCode");
$js_editor = $editor->build($sdkObj->getJSCode(), "", "cmEditor")->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$form = new simpleForm();
$CSSStyleForm = $form->build($moduleID, "updateModel", $controls = FALSE)->get();
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$input = $form->getInput($type = "hidden", $name = "lib", $obj_libName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Package
$input = $form->getInput($type = "hidden", $name = "pkg", $obj_packageName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Namespace
$input = $form->getInput($type = "hidden", $name = "ns", $obj_nsName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Object ID
$input = $form->getInput($type = "hidden", $name = "oid", $obj_objectName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Code Container
$obj_container = DOM::create();
$form->append($obj_container);

$objMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$objMgrToolbar->build($dock = navigationBar::TOP, $obj_container);
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
$editor = new WViewEditor("objectCSS", "objectModel");
$css_editor = $editor->build($structure, $code)->get();
DOM::append($objModelContainer, $css_editor);

// Send redWIDE Tab
$obj_id = $websideID."_".$obj_libName."_".$obj_packageName.($obj_nsName == "" ? "" : "_".$obj_nsName)."_".$obj_objectName;
$header = $obj_objectName;
$devTabber = new devTabber();
return $devTabber->getReportContent($obj_id, $header, $pageContent->get(), "srcTabber");
//#section_end#
?>