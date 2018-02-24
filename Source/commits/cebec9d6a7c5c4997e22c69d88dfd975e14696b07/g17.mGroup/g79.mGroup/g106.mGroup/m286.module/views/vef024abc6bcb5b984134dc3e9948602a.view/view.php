<?php
//#section#[header]
// Module Declaration
$moduleID = 286;

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
importer::import("API", "Model");
importer::import("DEV", "Websites");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \DEV\Websites\pages\wsPage;
use \UI\Developer\devTabber;
use \UI\Developer\codeMirror;
use \UI\Developer\editors\WViewEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;


// Get page variables
$websiteID = engine::getVar("id");
$pageFolder = engine::getVar("folder");
$pageName = engine::getVar("name");

$itemID = "p".$websiteID."_".$pageFolder."_".$pageName;
$itemID = str_replace("/", "_", $itemID);
$itemID = str_replace(".", "_", $itemID);

// Initialize object
$pageObject = new wsPage($websiteID, $pageFolder, $pageName);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("wsPage_".$itemID, "websitePageObjectEditor");


// Create Global Container
$editorContainer = DOM::create("div", "", "", "pgEditor");
$pageContent->append($editorContainer);

// Create Global Container Toolbar
$tlb = new sideBar();
$navToolbar = $tlb->build(sideBar::LEFT, $editorContainer)->get();
DOM::append($editorContainer, $navToolbar);

// Delete button
$delTool = DOM::create("div", "", "", "objTool delete");
$tlb->insertToolbarItem($delTool);
$attr = array();
$attr['id'] = $websiteID;
$attr['folder'] = $pageFolder;
$attr['name'] = $pageName;
$actionFactory->setModuleAction($delTool, $moduleID, "deletePage", "", $attr);

// Create main tabber
$tabber = new tabControl();
$mainViewTabber = $tabber->build($id = $itemID."_tbc", $full = FALSE, $withBorder = FALSE)->get();
DOM::append($editorContainer, $mainViewTabber);


// Create Tabs
//_____ pageDesigner
$tabID = $itemID."_designer";
$tabHeader = moduleLiteral::get($moduleID, "lbl_designer");
$pageDesignerContainer = DOM::create("div", "", "", "pageDesigner tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $pageDesignerContainer, $selected = TRUE);
//_____ pageSource
$tabID = $objID."_source";
$tabHeader = moduleLiteral::get($moduleID, "lbl_sourceCode");
$pageSourceContainer = DOM::create("div", "", "", "pageSource tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $pageSourceContainer, $selected = FALSE);
//_____ page Behavior
$tabID = $objID."_behavior";
$tabHeader = moduleLiteral::get($moduleID, "lbl_jsCode");
$pageJSContainer = DOM::create("div", "", "", "pageJS tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $pageJSContainer, $selected = FALSE);


// Source Code Tab
$form = new simpleForm();
$innerForm = $form->build($moduleID, "updateSource", FALSE)->get();
DOM::append($pageSourceContainer, $innerForm);

// Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);

// Page folder
$input = $form->getInput($type = "hidden", $name = "folder", $pageFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Page name
$input = $form->getInput($type = "hidden", $name = "name", $pageName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create('div');
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = navigationBar::TOP, $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

// Settings button
$settingsTool = DOM::create("div", "", "", "objTool settings");
$navBar->insertToolbarItem($settingsTool);


$editor = new codeMirror($type = codeMirror::PHP, $name = "pageSource");
$objectEditor = $editor->build($pageObject->getPHPCode(), "", "cmEditor")->get();
DOM::append($editorOuterContainer, $objectEditor);
$pageInfoContainer = DOM::create("div", "", "", "pageInfo tabPageContent noDisplay");
DOM::append($editorOuterContainer, $pageInfoContainer);



// Designer tab (cssEditor)
$form = new simpleForm();
$innerForm = $form->build($moduleID, "updateHTML", FALSE)->get();
DOM::append($pageDesignerContainer, $innerForm);

// Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);

// Page folder
$input = $form->getInput($type = "hidden", $name = "folder", $pageFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Page name
$input = $form->getInput($type = "hidden", $name = "name", $pageName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create();
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new WViewEditor($cssName = "pageCSS", $htmlName = "pageHTML");
$pageHtml = $pageObject->getHTML();
$pageCSS = $pageObject->getCSS();
$objectEditor = $editor->build($pageHtml, $pageCSS)->get();
DOM::append($editorOuterContainer, $objectEditor);



// JS Code Tab
$form = new simpleForm();
$innerForm = $form->build($moduleID, "updateJs", FALSE)->get();
DOM::append($pageJSContainer, $innerForm);

// Website ID
$input = $form->getInput($type = "hidden", $name = "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);

// Page folder
$input = $form->getInput($type = "hidden", $name = "folder", $pageFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Page name
$input = $form->getInput($type = "hidden", $name = "name", $pageName, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create();
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new codeMirror($type = codeMirror::JS, $name = "pageJS");
$objectEditor = $editor->build($pageObject->getJS(), "", "cmEditor")->get();
DOM::append($editorOuterContainer, $objectEditor);


// Page Settings
$headersContainer = module::loadView($moduleID, "pageSettings");
DOM::append($pageInfoContainer, $headersContainer);


// Get wide tabber
$WIDETab = new devTabber();
return $WIDETab->getReportContent($itemID, $pageName, $pageContent->get(), "pgsTabber");
//#section_end#
?>