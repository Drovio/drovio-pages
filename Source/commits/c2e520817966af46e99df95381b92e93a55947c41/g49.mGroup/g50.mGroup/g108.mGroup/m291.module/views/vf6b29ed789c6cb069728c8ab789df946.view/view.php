<?php
//#section#[header]
// Module Declaration
$moduleID = 291;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;

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
importer::import("ESS", "Protocol");
importer::import("DEV", "Prototype");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
use \API\Profile\team;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Modules\MPage;
use \UI\Navigation\treeView;
use \UI\Presentation\gridSplitter;
use \DEV\Prototype\sourceMap;

// Create Module Page
$page = new MPage($moduleID);
$GLOBALS["actFactory"] = $page->getActionFactory();
$GLOBALS["moduleID"] = $moduleID;

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "sdkManualPage", TRUE);



$targetcontainer = "sideMenu";
$targetgroup = "sideGroup";
$navgroup = "topNavGroup";

// Set top navigation
$navItem = HTML::select(".sdkManualContainer .topNav .hdNav")->item(0);
NavigatorProtocol::staticNav($navItem, "coreSDK", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$navItem = HTML::select(".sdkManualContainer .topNav .hdNav")->item(1);
NavigatorProtocol::staticNav($navItem, "webSDK", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$container = HTML::select("#coreSDK")->item(0);
NavigatorProtocol::selector($container, $targetgroup);

$container = HTML::select("#webSDK")->item(0);
NavigatorProtocol::selector($container, $targetgroup);



// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Get open packages
$openPackages = importer::getOpenPackageList();


// Redback Core SDK Explorer
$packageExplorer = HTML::select(".sdkManualPage #coreSDK")->item(0);

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
	// Filter libraries
	if (team::getTeamID() != 1)
		if (!isset($openPackages[$library]))
			continue;
			
	// Create group item container
	$item = DOM::create("div", "", "", "sdkLibrary");
	$itemIco = DOM::create("span", "", "", "contentIcon libIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $library);
	DOM::append($item, $itemName);
	
	$item = $navTree->insertExpandableTreeItem($library, $item);
	$navTree->assignSortValue($item, $library);
	buildPackageTree($navTree, $library, $sourceMap, $openPackages);
}


// Return output
return $page->getReport();






function buildPackageTree($navTree, $libName, $sourceMap, $openPackages)
{
	$packages = $sourceMap->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		// Filter packages
		if (team::getTeamID() != 1)
			if (!in_array($packageName, $openPackages[$libName]))
				continue;
			
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
		buildObjTree($navTree, $libName, $packageName, $parentNs, $sourceMap, $openPackages);
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

function buildObjTree($navTree, $libName, $packageName, $parentNs, $sourceMap)
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
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["moduleID"], "manualViewer", ".manualHolder", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
	}
}


return $HTMLModulePage->getReport();
//#section_end#
?>