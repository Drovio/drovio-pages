<?php
//#section#[header]
// Module Declaration
$moduleID = 174;

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
importer::import("DEV", "Apps");
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
use \DEV\Apps\components\source\sourceObject;
use \DEV\Documentation\classDocEditor;

// Initialize SDK Object
$sdkObj = new sourceObject($_GET['appID'], $_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $_GET['lib']."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Create Module Page
$HTMLContentBuilder = new MContent($moduleID);
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
DOM::append($globalContainer, $groupContainer);

// Create Source Code Form
$form = new simpleForm();
$sourceCodeForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "appID", $_GET['appID'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput("hidden", "lib", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$class_container = DOM::create();
$form->append($class_container);

$codeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$codeMgrToolbar->build($dock = "T", $class_container);
DOM::append($class_container, $codeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$codeMgrToolbar->insertToolbarItem($saveTool);

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
$JSCodeForm = $form->build($moduleID, "updateScript", $controls = FALSE)->get();
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "appID", $_GET['appID'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput("hidden", "lib", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$js_container = DOM::create();
$form->append($js_container);

$jsCodeMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$jsCodeMgrToolbar->build($dock = "T", $js_container);
DOM::append($js_container, $jsCodeMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
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
$CSSStyleForm = $form->build($moduleID, "updateModel", $controls = FALSE)->get();
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ App ID
$input = $form->getInput("hidden", "appID", $_GET['appID'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput("hidden", "lib", $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput("hidden", "pkg", $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput("hidden", "ns", $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput("hidden", "oid", $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$obj_container = DOM::create();
$form->append($obj_container);

$objMgrToolbar = new navigationBar();
// Create Source Code Manager Toolbar
$objMgrToolbar->build($dock = "T", $obj_container);
DOM::append($obj_container, $objMgrToolbar->get());

// Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "", "objModelContainer");
DOM::append($obj_container, $objModelContainer);


// CSS Model Editor
$code = $sdkObj->getCSSCode();
$structure = $sdkObj->getCSSModel();
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