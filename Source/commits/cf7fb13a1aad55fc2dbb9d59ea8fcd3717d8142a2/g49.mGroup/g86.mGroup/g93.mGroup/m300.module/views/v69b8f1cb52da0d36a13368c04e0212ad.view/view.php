<?php
//#section#[header]
// Module Declaration
$moduleID = 300;

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \UI\Navigation\treeView;
use \UI\Presentation\tabControl;
use \DEV\Apps\application;
use \DEV\WebExtensions\components\source\sourceLibrary;
use \DEV\WebExtensions\components\source\sourcePackage;

// Create HTMLContent Object
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$GLOBALS['actFactory'] = $actionFactory;
$GLOBALS['moduleID'] = $moduleID;
$GLOBALS['innerModules'] = $innerModules;

// Initialize Application
$appID = $_GET['appID'];
$devApp = new application($appID);

// Build page
$pageContent->build("", "extExplorer", TRUE);

// Create container
$sectionViewer = HTML::select("#extSectionViewer")->item(0);


// Toolbar with refresh button
$viewerToolbar = DOM::create("div", "", "", "extExplorerToolbar");
DOM::append($sectionViewer, $viewerToolbar);

$title = moduleLiteral::get($moduleID, "lbl_refresh");
$refreshBtn = DOM::create("div", $title, "refreshExt", "toolbarTool");
DOM::append($viewerToolbar, $refreshBtn);


// Application Views Tree
$navTree = new treeView();
$navTree->build("ExtExplorerTree");
$navTreeElement = $navTree->get();
DOM::append($sectionViewer, $navTreeElement);

// Application views
$views = $devApp->getViews();
$item = moduleLiteral::get($moduleID, "lbl_explorerItem_Views");
$navTree->insertExpandableTreeItem("appViews", $item);
foreach ($views as $view)
{
	$item = DOM::create("div", $view);
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $view;
	$treeItem = $navTree->insertTreeItem("v".$view, $item, "appViews");
	$actionFactory->setModuleAction($treeItem, $innerModules['viewEditor'], "", "", $attr);
}

// Application Source
$sdkLibrary = new sourceLibrary($appID);
$libraries = $sdkLibrary->getList();
$item = moduleLiteral::get($moduleID, "lbl_explorerItem_Source");
$navTree->insertExpandableTreeItem("appSource", $item);
foreach ($libraries as $library)
{
	// Library Tree View
	$item = DOM::create("div", $library);
	$navTree->insertExpandableTreeItem($library, $item, "appSource");

	// Build Package List
	buildPackageTree($appID, $navTree, $library);
}

// Application Styles
$styles = $devApp->getStyles();
$item = moduleLiteral::get($moduleID, "lbl_explorerItem_Styles");
$navTree->insertExpandableTreeItem("appStyles", $item);
foreach ($styles as $style)
{
	$item = DOM::create("div", $style.".css");
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $style;
	$treeItem = $navTree->insertTreeItem("s".$style, $item, "appStyles");
	$actionFactory->setModuleAction($treeItem, $moduleID, "styleEditor", "", $attr);
}

// Application Scripts
$scripts = $devApp->getScripts();
$item = moduleLiteral::get($moduleID, "lbl_explorerItem_Scripts");
$navTree->insertExpandableTreeItem("appScripts", $item);
foreach ($scripts as $script)
{
	$item = DOM::create("div", $script.".js");
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $script;
	$treeItem = $navTree->insertTreeItem("s".$script, $item, "appScripts");
	$actionFactory->setModuleAction($treeItem, $moduleID, "scriptEditor", "", $attr);
}


return $pageContent->getReport();


function buildPackageTree($appID, $navTree, $libName)
{
	$sdkLibrary = new sourceLibrary($appID);
	$packages = $sdkLibrary->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		$item = DOM::create("div", $packageName);
		$navTree->insertExpandableTreeItem($packageName, $item, $libName);
		
		// Get Namespaces
		$sdkp = new sourcePackage($appID);
		$nss = $sdkp->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($appID, $navTree, $nsName, $nsValue, $libName, $packageName, "");
		
		buildObjTree($appID, $navTree, $libName, $packageName, $parentNs);
	}
}

function buildNsTree($appID, $navTree, $nsName, $subElements, $libName, $packageName, $parentNs)
{
	if(!empty($parentNs)) 
	{
		$parentId = $parentNs;
		$itemId = $parentNs."_".$nsName;
	}
	else
	{
		$parentId = $packageName;
		$itemId = $nsName;
	}
	
	// Build the domain tree item
	$item = DOM::create("div", $nsName);
	$navTree->insertExpandableTreeItem($itemId, $item, $parentId);	
		
	//_____ Build the query tree list
	$parentNs = $itemId;
	buildObjTree($appID, $navTree, $libName, $packageName, $parentNs);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($appID, $navTree, $name, $value, $libName, $packageName, $parentNs);
}

function buildObjTree($appID, $navTree, $libName, $packageName, $parentNs)
{
	$sdkp = new sourcePackage($appID);
	$objs = $sdkp->getObjects($libName, $packageName, $parentNs);
	if (!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		if ($value['namespace'] != str_replace("_", "::", $parentNs))
			continue;
			
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['appID'] = $appID;
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$attr['oid'] = $value['name'];
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["innerModules"]["objectEditor"], "", "", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
	}
}
//#section_end#
?>