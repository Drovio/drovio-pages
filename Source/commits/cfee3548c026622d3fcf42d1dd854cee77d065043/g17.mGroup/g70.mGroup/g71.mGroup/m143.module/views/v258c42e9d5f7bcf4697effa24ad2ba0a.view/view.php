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
use \API\Developer\ebuilder\extComponents\extStyle;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\sidebar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;

$extensionObject = new extension();
// Try to Load	
$success = $extensionObject->load($_GET['id']);
if(!$success)
{
	//return Notification error. not loaded
	echo "Extension Not Loaded";
}

switch($_GET['type'])
{
	case 'theme' :
		// Tab id prefix
		$idPre = 'th_';
		// Load Code
		$themeObject = $extensionObject->getTheme($_GET['name']);  
		$objectName = $_GET['name'];
		$code = $themeObject->getSourceCode();
		// Save Action
		$sAction = 'saveTheme';
		// Commit Action
		$cAction = 'commitTheme';
		// Additional Control Flag
		$additionalControl = TRUE;
		break;
	case 'style' :
		// Tab id prefix
		$idPre = 'st_';		
		// Load Code
		$objectName = "MainStyle";
		$code = $extensionObject->getMainStyle();
		// Save Action
		$sAction = 'saveStyle';
		// Commit Action
		$cAction = 'commitStyle';
		// Additional Control Flag
		$additionalControl = FALSE;
		break;
	default :
		return $reporter->content_not_found(TRUE);
}

// Toolbar Control
$tlb = new sidebar();
$tlbItemBuilder = new toolbarItem();

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create form
$sForm = new simpleForm();
$sForm->build($innerModules['extensionObject'], $sAction, $controls = FALSE);
// Append form to Content
$HTMLContentBuilder->buildElement($sForm->get());

// ###Hidden Values
// ####Name
$input = $sForm->getInput($type = "hidden", $name = "name", $objectName, $class = "", $autofocus = FALSE);
$sForm->append($input);

// ####Extension Id
$input = $sForm->getInput($type = "hidden", $name = "id", $_GET['id'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// ###Content Wrapper
$obj_container = DOM::create();
$sForm->append($obj_container);

// ####Toolbar
// Create Source Code Manager Toolbar
$objMgrToolbar = $tlb->build($dock = "L", $obj_container)->get();
DOM::append($obj_container, $objMgrToolbar); 
 
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
$attr['name'] = $viewName;
$actionFactory->setModuleAction($commitTool, $innerModules['extensionObject'], $cAction, "", $attr);
DOM::append($codeGroup, $commitTool); 

// Css Style Code Editor
//$codeEditor = new codeEditor();
//$codeEditor->build("css", $code);
$codeEditor = new cssEditor("cssCode", "");
$codeEditor->build("", $code);
DOM::append($obj_container, $codeEditor->get());


// Prepare report
// Send redWIDE Tab
$obj_id = $idPre.$objectName;
$header = $_GET['type'].":".$objectName;
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $HTMLContentBuilder->get());
//#section_end#
?>