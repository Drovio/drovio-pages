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
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \API\Developer\components\ebuilder\ebLibrary;
use \API\Developer\components\ebuilder\ebPackage;
use \UI\Html\HTMLContent;
use \UI\Forms\form;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;

$GLOBALS["ebuilderObjectEditor"] = $innerModules['ebuilderObjectEditor'];

function buildSubTree($container, $navTree, $libName, $packageName, $name, $sub, $full_domain, $moduleID)
{
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$treeItem = $navTree->insert_expandableTreeItem($container, $full_domain, $item);
	//_____ Build the query tree list
	buildObjTree($navTree, $treeItem, $libName, $packageName, $full_domain, $moduleID);
	
	if (is_array($sub) & count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		buildSubTree($treeItem, $navTree, $libName, $packageName, $key, $value, $full_domain."_".$key, $moduleID);
}

function buildObjTree($navTree, $container, $libName, $packageName, $parentNs, $moduleID)
{
	$ebPkg = new ebPackage();
	$objs = $ebPkg->getNSObjects($libName, $packageName, $parentNs);

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['oid'] = $value['name'];
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		
		ModuleProtocol::addActionGET($item, $GLOBALS['ebuilderObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insert_treeItem($container, $key, $item);
	}
}

function buildPackageTree($libName, $navTree, $navTreeElement, $moduleID)
{
	$ebLib = new ebLibrary();
	$packages = $ebLib->getPackageList($libName);
	foreach ($packages as $key => $value)
	{
		$item = DOM::create("div", $key);
		$treeItem = $navTree->insert_expandableTreeItem($navTreeElement, $key, $item);
		
		// Get Namespaces
		$ebPkg = new ebPackage();
		$nss = $ebPkg->getNSList($libName, $key);
		$packageName = $key;
		
		foreach ($nss as $key => $value)
			buildSubTree($treeItem, $navTree, $libName, $packageName, $key, $value, $key, $moduleID);
		
		// Build package children objects
		buildObjTree($navTree, $treeItem, $libName, $packageName, "", $moduleID);
	}
}


// Instantiate an html report
$htmlFragment = new HTMLContent();
$apiContainer = $htmlFragment->build("", "viewerWrapper")->get();

// Create TabControl for Platform and CMS
$libraryTabber = new tabControl();
$libraryTabberControl = $libraryTabber->build($id = "tbr_libraryBrowser", TRUE)->get();

DOM::append($apiContainer, $libraryTabberControl);

// Get all libraries
$ebLib = new ebLibrary();
$libraries = $ebLib->getList();
$selected = TRUE;
foreach ($libraries as $library)
{
	// Library Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build($library."Tree")->get();
	$libraryTabber->insertTab($library, $library, $navTreeElement, $selected);
	
	// Build Package List
	buildPackageTree($library, $navTree, $navTreeElement, $moduleID);
	
	// Clear Selected
	$selected = FALSE;
}

return $htmlFragment->getReport();
//#section_end#
?>