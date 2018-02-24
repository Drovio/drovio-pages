<?php
//#section#[header]
// Module Declaration
$moduleID = 248;

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
importer::import("DEV", "Documentation");
importer::import("DEV", "WebEngine");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Developer\devTabber;
use \UI\Developer\codeEditor;
use \UI\Developer\editors\CSSEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\tabControl;
use \UI\Navigation\navigationBar;
use \DEV\WebEngine\sdk\webObject;
use \DEV\WebEngine\webCoreProject;
use \DEV\Documentation\classDocEditor;


// Initialize eBuilder Object
$ebObj = new webObject($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid']);
// Set Object ID
$objID = $_GET['lib']."_".$_GET['pkg']."_".($_GET['ns'] == "" ? "" : $_GET['ns']."_").$_GET['oid'];

// Toolbar Control
$tlb = new navigationBar();

// Create Module Page
$HTMLContentBuilder = new MContent($moduleID);
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create Global Container
$HTMLContentBuilder->build("", "objectGlobalContainer");
$globalContainer = $HTMLContentBuilder->get();

// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID, FALSE, FALSE)->get();
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
$form = new simpleForm();
$sourceCodeForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectSourceCodeContainer, $sourceCodeForm);

// Hidden Values
//_____ Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);

// Outer Code Container
$class_container = DOM::create();
$form->append($class_container);
navigationBar::setParent($class_container, $dock = "T");

// Create Source Code Manager Toolbar
$codeMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($class_container, $codeMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['id'] = webCoreProject::PROJECT_ID;
$attr['lib'] = $_GET['lib'];
$attr['pkg'] = $_GET['pkg'];
$attr['ns'] = $_GET['ns'];
$attr['oid'] = $_GET['oid'];
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteObject", "", $attr);


// Create Code Container (With Documentation) in a slider viewer
// Source Code Editor
$editor = new codeEditor();
$code = $ebObj->getSourceCode();
$class_editor = $editor->build("php", $code)->get();

// Class Documentor Container
$documentor = new classDocEditor($class_editor);
$manual = $ebObj->getSourceDoc();
$docWrapper = $documentor->build($manual)->get();
DOM::append($class_container, $docWrapper);


//_____ Create Javascript Code Form
$form = new simpleForm();
$JSCodeForm = $form->build($moduleID, "updateJS", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectJSCodeContainer, $JSCodeForm);

// Hidden Values
//_____ Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);


// Outer Code Container
$js_container = DOM::create();
$form->append($js_container);
navigationBar::setParent($js_container, $dock = "T");

// Create Source Code Manager Toolbar
$jsCodeMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($js_container, $jsCodeMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$JSCodeContainer = DOM::create("div", "", "", "JSCodeContainer");
DOM::append($js_container, $JSCodeContainer);

// Source Code Editor
$code = $ebObj->getJSCode();
$js_editor = $editor->build("js", $code)->get();
DOM::append($JSCodeContainer, $js_editor);

//_____ Create CSS Form
$form = new simpleForm();
$CSSStyleForm = $form->build($moduleID, "updateModel", $controls = FALSE)->get();
//_____ Append to group
DOM::append($objectModelContainer, $CSSStyleForm);

// Hidden Values
//_____ Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Library
$libIDElem = $form->getInput($type = "hidden", $name = "libID", $value = $_GET['lib'], $class = "", $autofocus = FALSE);
$form->append($libIDElem);
//_____ Package
$pkgIDElem = $form->getInput($type = "hidden", $name = "pkgID", $value = $_GET['pkg'], $class = "", $autofocus = FALSE);
$form->append($pkgIDElem);
//_____ Namespace
$nsIDElem = $form->getInput($type = "hidden", $name = "nsID", $value = $_GET['ns'], $class = "", $autofocus = FALSE);
$form->append($nsIDElem);
//_____ Object ID
$objIDElem = $form->getInput($type = "hidden", $name = "objID", $value = $_GET['oid'], $class = "", $autofocus = FALSE);
$form->append($objIDElem);


// Outer Code Container
$obj_container = DOM::create();
$form->append($obj_container);
navigationBar::setParent($obj_container, $dock = "T");

// Create Source Code Manager Toolbar
$objMgrToolbar = $tlb->build($dock = "T")->get();
DOM::append($obj_container, $objMgrToolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "", "objModelContainer");
DOM::append($obj_container, $objModelContainer);


// Source Code Editor
$code = $ebObj->getCSSCode();
$structure = $ebObj->getCSSModel();
$editor = new CSSEditor("objectCSS", "objectModel");
$css_editor = $editor->build($structure, $code)->get();
DOM::append($objModelContainer, $css_editor);


// Send redWIDE Tab
$obj_id = $_GET['pkg'].($_GET['ns'] == "" ? "" : "_".$_GET['ns'])."_".$_GET['oid'];
$header = $_GET['oid'];
$WIDETab = new devTabber();
return $WIDETab->getReportContent($obj_id, $header, $globalContainer);
//#section_end#
?>