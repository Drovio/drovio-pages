<?php
//#section#[header]
// Module Declaration
$moduleID = 265;

// Inner Module Codes
$innerModules = array();
$innerModules['styleEditor'] = 267;
$innerModules['scriptEditor'] = 268;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\treeView;
use \DEV\Apps\application;

// Create page content Object
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize Application
$appID = $_GET['appID'];
$devApp = new application($appID);

// Build page
$pageContainer = $pageContent->build("", "libExplorer", TRUE)->get();


// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "LRefresh", "lNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);


// Application Library Tree
$navTree = new treeView();
$navTreeElement = $navTree->build("appLibViewer")->get();
$pageContent->append($navTreeElement);

// Application Styles
$styles = $devApp->getStyles();
// Create group item container
$item = DOM::create("div", "", "", "libStyles");
$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
DOM::append($item, $itemIco);
$itemName = moduleLiteral::get($moduleID, "lbl_libExplorer_Styles");
DOM::append($item, $itemName);
$navTree->insertExpandableTreeItem("appStyles", $item);
foreach ($styles as $style)
{
	// Create group item container
	$item = DOM::create("div", "", "", "libStyle");
	$itemIco = DOM::create("span", "", "", "contentIcon flIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $style.".css");
	DOM::append($item, $itemName);
	
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $style;
	$treeItem = $navTree->insertTreeItem("s".$style, $item, "appStyles");
	$actionFactory->setModuleAction($treeItem, $innerModules['styleEditor'], "", "", $attr, TRUE);
}

// Application Scripts
$scripts = $devApp->getScripts();
// Create group item container
$item = DOM::create("div", "", "", "libScripts");
$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
DOM::append($item, $itemIco);
$itemName = moduleLiteral::get($moduleID, "lbl_libExplorer_Scripts");
DOM::append($item, $itemName);
$navTree->insertExpandableTreeItem("appScripts", $item);
foreach ($scripts as $script)
{
	$item = DOM::create("div", "", "", "libStyle");
	$itemIco = DOM::create("span", "", "", "contentIcon flIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $script.".js");
	DOM::append($item, $itemName);
	
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $script;
	$treeItem = $navTree->insertTreeItem("s".$script, $item, "appScripts");
	$actionFactory->setModuleAction($treeItem, $innerModules['scriptEditor'], "", "", $attr, TRUE);
}


return $pageContent->getReport();
//#section_end#
?>