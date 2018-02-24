<?php
//#section#[header]
// Module Declaration
$moduleID = 247;

// Inner Module Codes
$innerModules = array();
$innerModules['objectEditor'] = 248;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\form;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \DEV\WebEngine\sdk\webLibrary;
use \DEV\WebEngine\sdk\webPackage;
use \DEV\WebEngine\sdk\webObject;
use \DEV\WebEngine\webCoreProject;
use \DEV\Documentation\classDocumentor;


// Instantiate an html report
$pageContent = new MContent($moduleID); 
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "webSDKExplorer")->get();

// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "SDKRefresh", "sdkNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "sdkNavTool create_new")->get();
$codeMgrToolbar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = webCoreProject::PROJECT_ID;

// New library
$title = moduleLiteral::get($moduleID, "hd_createLibrary");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createLibrary", "", $attr);

// New package
$title = moduleLiteral::get($moduleID, "hd_createPackage");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createPackage", "", $attr);

// New namespace
$title = moduleLiteral::get($moduleID, "hd_createNamespace");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createNamespace", "", $attr);

// New object
$title = moduleLiteral::get($moduleID, "hd_createObject");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createObject", "", $attr);

// Library Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build("WebSDKTree")->get();
$pageContent->append($navTreeElement);

// Get all libraries
$ebLib = new webLibrary();
$libraries = $ebLib->getList();
foreach ($libraries as $library)
{
	// Create group item container
	$item = DOM::create("div", "", "", "sdkLibrary");
	$itemIco = DOM::create("span", "", "", "contentIcon libIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $library);
	DOM::append($item, $itemName);

	// Add tree item
	$item = $navTree->insertExpandableTreeItem($library, $item);
	$navTree->assignSortValue($item, $library);
		
	// Build Package List
	buildPackageTree($navTree, $library, $innerModules['objectEditor'], $actionFactory);
}


// Legend
$legend = DOM::create("div", "", "", "sdkLegend");
$pageContent->append($legend);
 
$healthy = DOM::create("span", "Healthy", "", "legendEntry");
DOM::append($legend, $healthy);
$updated = DOM::create("span", "Recently Updated", "", "legendEntry updatedObject");
DOM::append($legend, $updated);
$depr = DOM::create("span", "Deprecated", "", "legendEntry deprecatedObject");
DOM::append($legend, $depr);
$undoc = DOM::create("span", "Undocumented", "", "legendEntry undocumentedObject");
DOM::append($legend, $undoc);

return $pageContent->getReport();


function buildPackageTree($navTree, $libName, $moduleID, $actionFactory)
{
	$webLibrary = new webLibrary();
	$packages = $webLibrary->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "sdkPackage");
		$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $packageName);
		DOM::append($item, $itemName);
	
		// Add tree item
		$item = $navTree->insertExpandableTreeItem($packageName, $item, $libName);
		$navTree->assignSortValue($item, $packageName);
		
		// Get Namespaces
		$sdkp = new webPackage();
		$nss = $sdkp->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($navTree, $nsName, $nsValue, $libName, $packageName, "", $moduleID, $actionFactory);
		
		buildObjTree($navTree, $libName, $packageName, $parentNs, $moduleID, $actionFactory);
	}
}

function buildNsTree($navTree, $nsName, $subElements, $libName, $packageName, $parentNs, $moduleID, $actionFactory)
{
	if (!empty($parentNs)) 
	{
		$parentId = $parentNs;
		$itemId = $parentNs."_".$nsName;
	}
	else
	{
		$parentId = $packageName;
		$itemId = $nsName;
	}
	
	// Create group item container
	$item = DOM::create("div", "", "", "sdkNamespace");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $nsName);
	DOM::append($item, $itemName);
	
	// Build the domain tree item
	$item = $navTree->insertExpandableTreeItem($itemId, $item, $parentId);	
	$navTree->assignSortValue($item, ".".$nsName);
		
	//_____ Build the query tree list
	$parentNs = $itemId;
	buildObjTree($navTree, $libName, $packageName, $parentNs, $moduleID, $actionFactory);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($navTree, $name, $value, $libName, $packageName, $parentNs, $moduleID, $actionFactory);
}

function buildObjTree($navTree, $libName, $packageName, $parentNs, $moduleID, $actionFactory)
{
	$webPackage = new webPackage();
	$objs = $webPackage->getPackageObjects($libName, $packageName, $parentNs);
	if (!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		if ($value['namespace'] != str_replace("_", "::", $parentNs))
			continue;
		
		// Create group item container
		$item = DOM::create("div", "", "", "sdkObject");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $value['name']);
		DOM::append($item, $itemName);
			
		$attr = array();
		$attr['id'] = webCoreProject::PROJECT_ID;
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$actionFactory->setModuleAction($item, $moduleID, "", "", $attr, $loading = TRUE);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
		
		$sdkObj = new webObject($libName, $packageName, $parentNs, $value['name']);
		$cInfo = classDocumentor::getClassDetails($sdkObj->getSourceDoc());
		
		if (empty($cInfo))
			HTML::addClass($treeItem, "undocumentedObject");
		else if (!empty($cInfo['deprecated']))
			HTML::addClass($treeItem, "deprecatedObject");
		else if (!empty($cInfo['daterevised'])
			&& (time() - intval($cInfo['daterevised']) < 7*24*60*60 )) // 1 week
			HTML::addClass($treeItem, "updatedObject");
	}
}
//#section_end#
?>