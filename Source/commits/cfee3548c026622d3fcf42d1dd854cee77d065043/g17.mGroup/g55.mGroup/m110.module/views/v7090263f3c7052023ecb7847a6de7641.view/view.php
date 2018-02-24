<?php
//#section#[header]
// Module Declaration
$moduleID = 110;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Presentation");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\resources\layouts\systemLayout;
use \API\Developer\resources\layouts\ebuilderLayout;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Forms\templates\simpleForm;
use \ESS\Protocol\server\ModuleProtocol;
use \INU\Developer\redWIDE;
use \INU\Developer\cssEditor;

$group = $_GET['group'];
$layoutName = $_GET['name'];

switch($group)
{
	case 'ebuilder' :
		$layoutManager = new ebuilderLayout($layoutName);
		$wrapped = TRUE;
		break;
	case 'system' :
		$layoutManager = new systemLayout($layoutName);
		$wrapped = FALSE;
		break;
	default :
		break;	
}

// Toolbar Control
$tlb = new navigationBar();
$tlbItemBuilder = new toolbarItem();

// Create global layout object whapper
$globalObjectWhapper = DOM::create("div");
DOM::attr($globalObjectWhapper, "style", "height:100%;");

// #Object Content
// ##Create object Form
$objectForm_builder = new simpleForm();
$objectForm = $objectForm_builder->build($moduleID, "saveModel", $controls = FALSE)->get();
DOM::append($globalObjectWhapper, $objectForm);

// ###Hidden Values
// ####Group
$input = $objectForm_builder->getInput($type = "hidden", $name = "group", $group, $class = "", $autofocus = FALSE);
$objectForm_builder->append($input);
// ####Name
$input = $objectForm_builder->getInput($type = "hidden", $name = "name", $layoutName, $class = "", $autofocus = FALSE);
$objectForm_builder->append($input);

// ###Content Wrapper
$obj_container = DOM::create();
$objectForm_builder->append($obj_container);

// ####Toolbar
// Create Source Code Manager Toolbar
$objMgrToolbar = $tlb->build($dock = "T", $obj_container)->get();
DOM::append($obj_container, $objMgrToolbar);

// ###Code Group Tools
$codeGroup = DOM::create("div", "", "", "toolGroup codeGroup");
DOM::append($objMgrToolbar, $codeGroup);

// ####headers
$content = DOM::create("div", "", "", "sideTool save");
$saveTool = $tlbItemBuilder->build($content)->get();
ModuleProtocol::addActionPOST($saveTool, $moduleID, "saveLayout");
DOM::append($codeGroup, $saveTool);


// Css Style Code Editor
$css_codeEditor = new cssEditor("objectCSS", "objectXML");
$code = $layoutManager->getModel();
$structure = $layoutManager->getStructure(TRUE, $wrapped);
$css_editor = $css_codeEditor->build($structure, $code)->get();
DOM::append($obj_container, $css_editor);

// Send redWIDE Tab
$obj_id = $group."_".$layoutName;
$header = $layoutName;
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $globalObjectWhapper);
//#section_end#
?>