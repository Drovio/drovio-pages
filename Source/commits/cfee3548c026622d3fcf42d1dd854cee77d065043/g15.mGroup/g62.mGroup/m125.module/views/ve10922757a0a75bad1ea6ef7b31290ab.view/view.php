<?php
//#section#[header]
// Module Declaration
$moduleID = 125;

// Inner Module Codes
$innerModules = array();
$innerModules['appEditor'] = 132;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \API\Developer\components\appcenter\appLibrary;
use \API\Developer\components\appcenter\appPackage;
use \UI\Html\HTMLContent;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;

$GLOBALS["appEditor"] = $innerModules['appEditor'];


// Create HTML Content
$content = new HTMLContent();

// Library Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build("appObjectViewer", "appObjectViewer", TRUE)->get();
$container = $content->buildElement($navTreeElement)->get();

// Build Package List
$library = "ACL";
build_packageTree($library, $navTree, $navTreeElement, $moduleID);

function build_sub_tree($container, $navTree, $libName, $packageName, $name, $sub, $full_domain, $moduleID)
{
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$treeItem = $navTree->insert_expandableTreeItem($container, $full_domain, $item);
	//_____ Build the query tree list
	build_obj_tree($navTree, $treeItem, $libName, $packageName, $full_domain, $moduleID);
	
	if (is_array($sub) && count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		build_sub_tree($treeItem, $navTree, $libName, $packageName, $key, $value, $full_domain."_".$key, $moduleID);
}

function build_obj_tree($navTree, $container, $libName, $packageName, $parentNs, $moduleID)
{
	$appPkg = new appPackage();
	$objs = $appPkg->getNSObjects($libName, $packageName, $parentNs);	

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['oid'] = $key;
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		ModuleProtocol::addActionGET($item, $GLOBALS['appEditor'], "", "", $attr);
		$treeItem = $navTree->insert_treeItem($container, $key, $item);
	}
}

function build_packageTree($libName, $navTree, $navTreeElement, $moduleID)
{
	$appLib = new appLibrary();
	$packages = $appLib->getPackageList($libName);
	foreach ($packages as $key => $value)
	{
		$item = DOM::create("div", $key);
		$treeItem = $navTree->insert_expandableTreeItem($navTreeElement, $key, $item);
		
		// Get Namespaces
		$appPkg = new appPackage();
		$nss = $appPkg->getNSList($libName, $key);
		$packageName = $key;
		
		foreach ($nss as $key => $value)
			build_sub_tree($treeItem, $navTree, $libName, $packageName, $key, $value, $key, $moduleID);
		
		// Build package children objects
		build_obj_tree($navTree, $treeItem, $libName, $packageName, "", $moduleID);
	}
}

// Get Report
return $content->getReport();
//#section_end#
?>