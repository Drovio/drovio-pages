<?php
//#section#[header]
// Module Declaration
$moduleID = 263;

// Inner Module Codes
$innerModules = array();
$innerModules['srcObjectEditor'] = 264;

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
importer::import("API", "Literals");
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
use \DEV\Apps\source\srcPackage;

// Create page content Object
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Set globals
$GLOBALS['aFactory'] = $actionFactory;
$GLOBALS['srcObjectEditor'] = $innerModules['srcObjectEditor'];

// Initialize Application
$appID = $_GET['appID'];
$devApp = new application($appID);

// Build page
$pageContainer = $pageContent->build("", "packageExplorer", TRUE)->get();


// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "SRCRefresh", "srcNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);


// Application Views Tree
$navTree = new treeView();
$packageViewer = $navTree->build("appPackageViewer")->get();
$pageContent->append($packageViewer);

// Get packages
$sdkp = new srcPackage($appID);
$packages = $sdkp->getList();
foreach ($packages as $packageName => $value)
{
	// Create group item container
	$item = DOM::create("div", "", "", "srcPackage");
	$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $packageName);
	DOM::append($item, $itemName);

	// Add tree item
	$item = $navTree->insertExpandableTreeItem($packageName, $item);
	$navTree->assignSortValue($item, $packageName);

	// Build Namespace and Object List
	$nss = $sdkp->getNSList($packageName);
	foreach ($nss as $nsName => $nsValue)
		buildNsTree($appID, $navTree, $nsName, $nsValue, $packageName, "");
	
	buildObjTree($appID, $navTree, $packageName, $parentNs);
}


return $pageContent->getReport();

function buildNsTree($appID, $navTree, $nsName, $subElements, $packageName, $parentNs)
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
	
	// Create group item container
	$item = DOM::create("div", "", "", "srcNamespace");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $nsName);
	DOM::append($item, $itemName);
	
	// Build the domain tree item
	$item = $navTree->insertExpandableTreeItem($itemId, $item, $parentId);
	$navTree->assignSortValue($item, ".".$nsName);
		
	//_____ Build the query tree list
	$parentNs = $itemId;
	buildObjTree($appID, $navTree, $packageName, $parentNs);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($appID, $navTree, $name, $value, $packageName, $parentNs);
}

function buildObjTree($appID, $navTree, $packageName, $parentNs)
{
	$sdkp = new srcPackage($appID);
	$objs = $sdkp->getObjects($packageName, $parentNs);
	if (!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		if ($value['namespace'] != str_replace("_", "::", $parentNs))
			continue;
			
		// Create group item container
		$item = DOM::create("div", "", "", "srcObject");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $value['name']);
		DOM::append($item, $itemName);
		
		$attr = array();
		$attr['appID'] = $appID;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$attr['oid'] = $value['name'];
		$GLOBALS["aFactory"]->setModuleAction($item, $GLOBALS["srcObjectEditor"], "", "", $attr, TRUE);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
	}
}
//#section_end#
?>