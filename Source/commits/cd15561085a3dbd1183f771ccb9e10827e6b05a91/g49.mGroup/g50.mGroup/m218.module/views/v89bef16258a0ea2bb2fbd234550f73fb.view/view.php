<?php
//#section#[header]
// Module Declaration
$moduleID = 218;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Prototype");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Navigation\treeView;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \DEV\Prototype\sourceMap;

// Create Module Page
$page = new MPage($moduleID);
$GLOBALS["actFactory"] = $page->getActionFactory();
$GLOBALS["moduleID"] = $moduleID;

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "sdkManualReferencePage", TRUE);
$uiMainContent = HTML::select(".uiMainContent")->item(0);

$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Package Tree")->get();
DOM::append($uiMainContent, $viewer);

// Documentation Container
$docViewer = DOM::create("div", "", "docViewer");
$splitter->appendToMain($docViewer);

// Sidebar Tree
$packageExplorer = DOM::create("div", "", "", "classNavigator");
$splitter->appendToSide($packageExplorer);


// Library Tree View 
$navTree = new treeView();
$navTree->build("SDK_Tree", "", TRUE);
$navTreeElement = $navTree->get();
DOM::append($packageExplorer, $navTreeElement);

// Get all libraries
$sourceMap = new sourceMap(systemRoot."/System/Resources/Documentation/SDK/");
$libraries = $sourceMap->getLibraryList();
foreach ($libraries as $library)
{
	// Create group item container
	$item = DOM::create("div", "", "", "sdkLibrary");
	$itemIco = DOM::create("span", "", "", "contentIcon libIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $library);
	DOM::append($item, $itemName);
	
	$item = $navTree->insertExpandableTreeItem($library, $item);
	$navTree->assignSortValue($item, $library);
	buildPackageTree($navTree, $library, $sourceMap, $shared);
}

// Return output
return $page->getReport();


function buildPackageTree($navTree, $libName, $sourceMap, $shared)
{
	$packages = $sourceMap->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "sdkPackage");
		$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $packageName);
		DOM::append($item, $itemName);
		
		$item = $navTree->insertExpandableTreeItem($packageName, $item, $libName);
		$navTree->assignSortValue($item, ".".$packageName);
		
		// Get Namespaces
		$nss = $sourceMap->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($navTree, $nsName, $nsValue, $libName, $packageName, "", $sourceMap);
		
		$parentNs = str_replace("_", "::", $parentNs);
		buildObjTree($navTree, $libName, $packageName, $parentNs, $sourceMap, $shared);
	}
}

function buildNsTree($navTree, $nsName, $subElements, $libName, $packageName, $parentNs, $sourceMap)
{
	if (!empty($parentNs)) 
	{
		$parentId = str_replace("::", "_", $parentNs);
		$itemId = $parentNs."_".$nsName;
	}
	else
	{
		$parentId = $packageName;
		$itemId = $nsName;
	}
	
	// Create group item container
	$item = DOM::create("div", "", "", "sdkNs");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $nsName);
	DOM::append($item, $itemName);
	
	$item = $navTree->insertExpandableTreeItem($itemId, $item, $parentId);
	$navTree->assignSortValue($item, ".".$nsName);
		
	//_____ Build the query tree list
	buildObjTree($navTree, $libName, $packageName, $itemId, $sourceMap);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($navTree, $name, $value, $libName, $packageName, $itemId, $sourceMap);
}

function buildObjTree($navTree, $libName, $packageName, $parentNs, $sourceMap, $shared)
{
	$objs = $sourceMap->getObjectList($libName, $packageName, str_replace("_", "::", $parentNs));
	if(!empty($parentNs))
		$parentId = str_replace("::", "_", $parentNs);
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		$objName = (empty($parentNs) ? "" : $parentNs."::").$value['name'];
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
		$attr['domain'] = "SDK";
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["moduleID"], "manualViewer", "", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
	}
}


return $HTMLModulePage->getReport();
//#section_end#
?>