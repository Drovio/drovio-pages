<?php
//#section#[header]
// Module Declaration
$moduleID = 234;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkObjectEditor'] = 235;

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
importer::import("DEV", "Projects");
importer::import("DEV", "Core");
importer::import("DEV", "Documentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \DEV\Projects\project;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\sdk\sdkPackage;
use \DEV\Core\sdk\sdkObject;
use \DEV\Documentation\classDocumentor;


$GLOBALS["sdkObjectEditor"] = $innerModules['sdkObjectEditor'];

$pageContent = new MContent($moduleID);
$pageContainer = $pageContent->build("", "sdkPackageViewer")->get();
$GLOBALS["actFactory"] = $pageContent->getActionFactory();


// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "SDKRefresh", "sdkNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Get all libraries
$sdkLibrary = new sdkLibrary();
$libraries = $sdkLibrary->getList();
$selected = TRUE;

// Library Tree View
$navTree = new treeView();
$navTree->build("sdkExplorer", "", TRUE);
$navTreeElement = $navTree->get();
$pageContent->append($navTreeElement);

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
	buildPackageTree($navTree, $library);	
	
	// Clear Selected
	$selected = FALSE;
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




// Return output
return $pageContent->getReport();


function buildPackageTree($navTree, $libName)
{
	$sdkLibrary = new sdkLibrary();
	$packages = $sdkLibrary->getPackageList($libName);
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
		$sdkp = new sdkPackage();
		$nss = $sdkp->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($navTree, $nsName, $nsValue, $libName, $packageName, "");
		
		buildObjTree($navTree, $libName, $packageName, $parentNs);
	}
}

function buildNsTree($navTree, $nsName, $subElements, $libName, $packageName, $parentNs)
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
	buildObjTree($navTree, $libName, $packageName, $parentNs);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($navTree, $name, $value, $libName, $packageName, $parentNs);
}

function buildObjTree($navTree, $libName, $packageName, $parentNs)
{
	$sdkp = new sdkPackage();
	$objs = $sdkp->getPackageObjects($libName, $packageName, $parentNs);
	if(!empty($parentNs))
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
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["sdkObjectEditor"], "", "", $attr, TRUE);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
		
		$sdkObj = new sdkObject($libName, $packageName, $parentNs, $value['name']);
		$cInfo = classDocumentor::getClassDetails($sdkObj->getSourceDoc());
		
		if (empty($cInfo))
			DOM::appendAttr($treeItem, "class", "undocumentedObject");
		else if (!empty($cInfo['deprecated']))
			DOM::appendAttr($treeItem, "class", "deprecatedObject");
		else if (!empty($cInfo['daterevised'])
			&& (time() - intval($cInfo['daterevised']) < 7*24*60*60 )) // 1 week
			DOM::appendAttr($treeItem, "class", "updatedObject");
	}
}
//#section_end#
?>