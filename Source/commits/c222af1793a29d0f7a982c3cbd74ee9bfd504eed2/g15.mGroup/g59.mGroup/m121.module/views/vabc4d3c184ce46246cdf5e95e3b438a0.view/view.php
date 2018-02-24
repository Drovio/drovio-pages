<?php
//#section#[header]
// Module Declaration
$moduleID = 121;

// Inner Module Codes
$innerModules = array();
$innerModules['ebuilderObjectEditor'] = 134;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \DEV\WebEngine\sdk\webLibrary;
use \DEV\WebEngine\sdk\webPackage;
use \UI\Html\HTMLContent;
use \UI\Forms\form;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;

$GLOBALS["ebuilderObjectEditor"] = $innerModules['ebuilderObjectEditor'];


// Instantiate an html report
$pageContent = new HTMLContent(); 
$actionFactory = $pageContent->getActionFactory();
$apiContainer = $pageContent->build("", "viewerWrapper")->get();

// Create TabControl for Platform and CMS
$libraryTabber = new tabControl();
$libraryTabberControl = $libraryTabber->build($id = "tbr_libraryBrowser", TRUE)->get();
DOM::append($apiContainer, $libraryTabberControl);

// Get all libraries
$ebLib = new webLibrary();
$libraries = $ebLib->getList();
$selected = TRUE;
foreach ($libraries as $library)
{
	// Library Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build($library."Tree")->get();
	$libraryTabber->insertTab($library, $library, $navTreeElement, $selected);
	
	// Build Package List
	buildPackageTree($library, $navTree, $navTreeElement, $moduleID, $actionFactory);
	
	// Clear Selected
	$selected = FALSE;
}

return $pageContent->getReport();






function buildSubTree($container, $navTree, $libName, $packageName, $name, $sub, $full_domain, $moduleID, $actionFactory)
{
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$treeItem = $navTree->insert_expandableTreeItem($container, $full_domain, $item);
	//_____ Build the query tree list
	buildObjTree($navTree, $treeItem, $libName, $packageName, $full_domain, $moduleID, $actionFactory);
	
	if (is_array($sub) & count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		buildSubTree($treeItem, $navTree, $libName, $packageName, $key, $value, $full_domain."_".$key, $moduleID, $actionFactory);
}

function buildObjTree($navTree, $container, $libName, $packageName, $parentNs, $moduleID, $actionFactory)
{
	$ebPkg = new webPackage();
	$objs = $ebPkg->getPackageObjects($libName, $packageName, $parentNs);

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		
		$actionFactory->setModuleAction($item, $GLOBALS['ebuilderObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insert_treeItem($container, $key, $item);
	}
}

function buildPackageTree($libName, $navTree, $navTreeElement, $moduleID, $actionFactory)
{
	$ebLib = new webLibrary();
	$packages = $ebLib->getPackageList($libName);
	foreach ($packages as $key => $value)
	{
		$item = DOM::create("div", $key);
		$treeItem = $navTree->insert_expandableTreeItem($navTreeElement, $key, $item);
		
		// Get Namespaces
		$ebPkg = new webPackage();
		$nss = $ebPkg->getNSList($libName, $key);
		$packageName = $key;
		
		foreach ($nss as $key => $value)
			buildSubTree($treeItem, $navTree, $libName, $packageName, $key, $value, $key, $moduleID, $actionFactory);
		
		// Build package children objects
		buildObjTree($navTree, $treeItem, $libName, $packageName, "", $moduleID, $actionFactory);
	}
}
//#section_end#
?>