<?php
//#section#[header]
// Module Declaration
$moduleID = 110;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Layout");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Forms\templates\simpleForm;
use \UI\Layout\pageLayout;
use \UI\Modules\MContent;
use \INU\Developer\redWIDE;
use \INU\Developer\cssEditor;


$pageContent = new MContent();
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "layoutEditorContainer")->get();

// Init page layout object
$category = $_GET['category'];
$layoutName = $_GET['name'];
$layoutManager = new pageLayout($category, $layoutName);

// Toolbar Control
$tlb = new navigationBar();
$tlbItemBuilder = new toolbarItem();

// Layout Form
$form = new simpleForm();
$objectForm = $form->build($moduleID, "updateLayout", $controls = FALSE)->get();
$pageContent->append($objectForm);

// Hidden Values
$input = $form->getInput($type = "hidden", $name = "category", $category, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $layoutName, $class = "", $autofocus = FALSE);
$form->append($input);


// Editor Outer WRapper
$editorWrapper = DOM::create("div", "", "", "editorWrapper");
$form->append($editorWrapper);

// Build Toolbar
$navToolbar = $tlb->build(navigationBar::TOP, $editorWrapper)->get();
DOM::append($editorWrapper, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['category'] = $category;
$attr['name'] = $layoutName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteLayout", "", $attr);


// Css Style Code Editor
$css_codeEditor = new cssEditor("objectCSS", "objectXML");
$css = $layoutManager->getCSS();
$structure = $layoutManager->getStructure(TRUE, $wrapped);
$css_editor = $css_codeEditor->build($structure, $css)->get();
DOM::append($editorWrapper, $css_editor);

// Send redWIDE Tab
$obj_id = $group."_".$layoutName;
$header = $layoutName;
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $pageContainer);
//#section_end#
?>