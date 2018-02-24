<?php
//#section#[header]
// Module Declaration
$moduleID = 143;

// Inner Module Codes
$innerModules = array();
$innerModules['extensionObject'] = 142;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extension;
use \API\Developer\ebuilder\extComponents\extScript;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\sidebar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;

$objectName = $_GET['name'];
$extensionID = $_GET['id'];

$extensionObject = new extension();
// Try to Load	
$success = $extensionObject->load($_GET['id']);
if(!$success)
{
	//return Notification error. not loaded
	echo "Extension Not Loaded";
}

$scriptObject = $extensionObject->getScript($_GET['name']); 

// Toolbar Control
$tlb = new sidebar();
$tlbItemBuilder = new toolbarItem();

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create form
$sForm = new simpleForm();
$sForm->build($innerModules['extensionObject'], "saveJsScript", $controls = FALSE);
// Append form to Content
$HTMLContentBuilder->buildElement($sForm->get());

// ###Hidden Values
// ####Name
$input = $sForm->getInput($type = "hidden", $name = "name", $_GET['name'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// ####Extension Id
$input = $sForm->getInput($type = "hidden", $name = "id", $_GET['id'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// ###Content Wrapper
$obj_container = DOM::create();
$sForm->append($obj_container);

// ####Toolbar
// Create Source Code Manager Toolbar
$tlb->build($dock = "L", $obj_container);
DOM::append($obj_container, $tlb->get());  

// #####Save Tool
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
//PopupProtocol::addAction($saveTool, $innerModules['extensionObject'], $action);
$tlb->insertTool($saveTool);

// ####Commit
$content = DOM::create("div", "", "", "sideTool commit");
$commitTool = $tlbItemBuilder->build($content)->get();
$attr = array();
$attr['id'] = $_GET['id'];
$attr['name'] = $_GET['name'];
$actionFactory->setModuleAction($commitTool, $innerModules['extensionObject'], "commitJsScript", "", $attr);
DOM::append($codeGroup, $commitTool); 

// Css Style Code Editor
$codeEditor = new codeEditor();
$codeEditor->build("js", $scriptObject->getSourceCode());
DOM::append($obj_container, $codeEditor->get());


// Prepare report
// Send redWIDE Tab
$obj_id = "js_".$_GET['name'];
$header = "Script".":".$_GET['name'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $HTMLContentBuilder->get());
//#section_end#
?>