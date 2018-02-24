<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \INU\Developer\redWIDE;
use \INU\Developer\cssEditor;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Forms\templates\simpleForm;


$themeName = $_GET['theme'];
$pageStructureName = $_GET['pageStructure'];
$templateID = $_GET['templateId'];

// Try to load Object
$templateManager = new templateManager();
$templateObject = $templateManager->getTemplate($templateID);

// Create global layout object whapper
$globalObjectWhapper = DOM::create("div");

// Add attributes to global whapper		
DOM::attr($globalObjectWhapper, "style", "height:100%;");


// #Object Content
// ##Create object Form
$sForm = new simpleForm();
$sForm->build($innerModules['templateObject'], "savePageStructure", $controls = FALSE);
DOM::append($globalObjectWhapper, $sForm->get());

// ###Hidden Values
// ####Name
$input = $sForm->getInput($type = "hidden", $name = "name", $pageStructureName, $class = "", $autofocus = FALSE);
$sForm->append($input);


// ###Content Wrapper
$obj_container = DOM::create('div');
$sForm->append($obj_container);

// ##Toolbar
// Toolbar Control 
$tlb = new navigationBar();
$tlbItemBuilder = new toolbarItem();
// Create Source Code Manager Toolbar;
$sideToolArea = $tlb->build($dock = "T", $obj_container)->get();
DOM::append($obj_container, $sideToolArea); 


// #####Save Tool
$content = DOM::create("button", "",  "saveModel_".$_GET['pageStructure'], "sideTool save saveModule");
DOM::attr($content, "type", "submit");
$saveTool = $tlbItemBuilder->build($content)->get();
$tlb->insertTool($saveTool);

// Css Style Code Editor
$css_codeEditor= new cssEditor("objectCSS", "objectXML");
$code = $templateObject->getThemeCSS($themeName);
$structure = $templateObject->getStructureXML($pageStructureName, TRUE);
$css_editor = $css_codeEditor->build($structure, $code)->get();
DOM::append($obj_container, $css_editor);


// Prepare report
// Send redWIDE Tab
$obj_id = "th_".$themeName."_".$pageStructureName;
$header = $themeName.":".$pageStructureName;
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $globalObjectWhapper);
//#section_end#
?>