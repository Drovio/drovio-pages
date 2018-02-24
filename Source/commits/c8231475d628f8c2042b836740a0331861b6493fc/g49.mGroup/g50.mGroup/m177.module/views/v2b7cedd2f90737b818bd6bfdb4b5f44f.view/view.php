<?php
//#section#[header]
// Module Declaration
$moduleID = 177;

// Inner Module Codes
$innerModules = array();
$innerModules['DocsHomePage'] = 99;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Prototype");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Navigation\treeView;
use \UI\Html\HTMLModulePage;
use \DEV\Prototype\sourceMap;
use \DEV\Apps\appManager;

// Create Module Page
$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarFullscreen");
$GLOBALS["actFactory"] = $HTMLModulePage->getActionFactory();
$GLOBALS["moduleID"] = $innerModules["DocsHomePage"];
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$HTMLModulePage->build($pageTitle, "documentationPage");

// Main Container
$container = DOM::create("div", "", "", "docViewerWrapper");
$HTMLModulePage->appendToSection("mainContent", $container);

// Documentation Container
$docViewer = DOM::create("div", "", "docViewer");
DOM::append($container, $docViewer);

// Sidebar Tree
$classNavigator = DOM::create("div", "", "", "classNavigator");
$HTMLModulePage->appendToSection("sidebar", $classNavigator);





// Library Tree View 
$navTree = new treeView();
$navTree->build("SDK_Tree");
$navTreeElement = $navTree->get();
DOM::append($classNavigator, $navTreeElement);

// Get shared library list
$shared = appManager::getSharedLibraryList();

// Get all libraries
$sourceMap = new sourceMap(systemRoot."/System/Resources/Documentation/SDK/");
$libraries = $sourceMap->getLibraryList();
foreach ($libraries as $library)
{
	// Build Package List
	$item = DOM::create("span", $library);
	$item = $navTree->insertExpandableTreeItem($library, $item);
	$navTree->assignSortValue($item, $library);
	buildPackageTree($navTree, $library, $sourceMap, $shared);
}





// Return output
return $HTMLModulePage->getReport();


function buildPackageTree($navTree, $libName, $sourceMap, $shared)
{
	$packages = $sourceMap->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		$item = DOM::create("div", $packageName);
		$item = $navTree->insertExpandableTreeItem($packageName, $item, $libName);
		$navTree->assignSortValue($item, $packageName);
		
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
	
	// Build the domain tree item
	$item = DOM::create("div", $nsName);
	$item = $navTree->insertExpandableTreeItem($itemId, $item, $parentId);
	$navTree->assignSortValue($item, $nsName);
		
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
			
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$attr['domain'] = "SDK";
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["moduleID"], "objectViewer", "", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
	}
}


return $HTMLModulePage->getReport();
//#section_end#
?>