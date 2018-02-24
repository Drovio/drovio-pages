<?php
//#section#[header]
// Module Declaration
$moduleID = 385;

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \DEV\Websites\pages\sPage;
use \UI\Developer\devTabber;
use \UI\Developer\codeMirror;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;


// Get page variables
$websiteID = engine::getVar("id");
$pageFolder = engine::getVar("folder");
$pageName = engine::getVar("name");

$itemID = "p".$websiteID."_".$pageFolder."_".$pageName;
$itemID = str_replace("/", "_", $itemID);
$itemID = str_replace(".", "_", $itemID);

// Initialize object
$pageObject = new sPage($websiteID, $pageFolder, $pageName);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("sPage_".$itemID, "simplePageObjectEditor");

// Source Code Tab
$form = new simpleForm();
$pageForm = $form->build()->engageModule($moduleID, "updateContents")->get();
$pageContent->append($pageForm);

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
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Delete button
$delTool = DOM::create("div", "", "", "objTool delete");
$navBar->insertToolbarItem($delTool);
$attr = array();
$attr['id'] = $websiteID;
$attr['folder'] = $pageFolder;
$attr['name'] = $pageName;
$actionFactory->setModuleAction($delTool, $moduleID, "deletePage", "", $attr);

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$path_parts = pathinfo($pageName);
$type = $path_parts['extension'];

$editor = new codeMirror($type = codeMirror::PHP, $name = "pageContents");
$objectEditor = $editor->build($pageObject->get(), "", "cmEditor")->get();
DOM::append($editorOuterContainer, $objectEditor);


// Get wide tabber
$WIDETab = new devTabber();
return $WIDETab->getReportContent($itemID, $pageName, $pageContent->get(), "pgsTabber");
//#section_end#
?>