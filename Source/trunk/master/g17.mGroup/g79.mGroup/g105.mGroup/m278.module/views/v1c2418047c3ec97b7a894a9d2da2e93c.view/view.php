<?php
//#section#[header]
// Module Declaration
$moduleID = 278;

// Inner Module Codes
$innerModules = array();
$innerModules['srcObjectEditor'] = 280;

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
importer::import("DEV", "Websites");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \DEV\Websites\website;
use \DEV\Websites\source\srcLibrary;
use \DEV\Websites\source\srcPackage;
use \DEV\Websites\source\srcObject;

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Create module content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContainer = $pageContent->build("", "srcPackageExplorer")->get();

// Set globals
$GLOBALS["actFactory"] = $actionFactory;
$GLOBALS["srcObjectEditor"] = $innerModules['srcObjectEditor'];

// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "SourceRefresh", "srcNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "srcNavTool create_new")->get();
$codeMgrToolbar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = $projectID;

// New library
$title = moduleLiteral::get($moduleID, "hd_newLibrary");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createLibrary", "", $attr);

// New package
$title = moduleLiteral::get($moduleID, "hd_newPackage");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createPackage", "", $attr);

// New namespace
$title = moduleLiteral::get($moduleID, "hd_newNamespace");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createNamespace", "", $attr);

// New object
$title = moduleLiteral::get($moduleID, "hd_newObject");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createObject", "", $attr);

// Get all libraries
$srcLibrary = new srcLibrary($projectID);
$libraries = $srcLibrary->getList();
$selected = TRUE;

// Library Tree View
$navTree = new treeView();
$navTree->build("sourceExplorer", "", TRUE);
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
	buildPackageTree($projectID, $navTree, $library);	
	
	// Clear Selected
	$selected = FALSE;
}




// Return output
return $pageContent->getReport();


function buildPackageTree($websiteID, $navTree, $libName)
{
	$srcLibrary = new srcLibrary($websiteID);
	$packages = $srcLibrary->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "srcPackage");
		$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $packageName);
		DOM::append($item, $itemName);
	
		// Add tree item
		$item = $navTree->insertExpandableTreeItem($packageName, $item, $libName);
		$navTree->assignSortValue($item, $packageName);
		
		// Get Namespaces
		$srcPackage = new srcPackage($websiteID);
		$nss = $srcPackage->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($websiteID, $navTree, $nsName, $nsValue, $libName, $packageName, "");
		
		buildObjTree($websiteID, $navTree, $libName, $packageName, $parentNs);
	}
}

function buildNsTree($websiteID, $navTree, $nsName, $subElements, $libName, $packageName, $parentNs)
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
	buildObjTree($websiteID, $navTree, $libName, $packageName, $parentNs);
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($websiteID, $navTree, $name, $value, $libName, $packageName, $parentNs);
}

function buildObjTree($websiteID, $navTree, $libName, $packageName, $parentNs)
{
	$srcPackage = new srcPackage($websiteID);
	$objs = $srcPackage->getObjects($libName, $packageName, $parentNs);
	if(!empty($parentNs))
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
		$attr['id'] = $websiteID;
		$attr['wid'] = $websiteID;
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["srcObjectEditor"], "", "", $attr, TRUE);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		$navTree->assignSortValue($treeItem, $value['name']);
	}
}
//#section_end#
?>