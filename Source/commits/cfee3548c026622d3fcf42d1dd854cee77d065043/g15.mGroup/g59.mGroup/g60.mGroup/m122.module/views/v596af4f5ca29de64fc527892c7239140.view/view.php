<?php
//#section#[header]
// Module Declaration
$moduleID = 122;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
use \UI\Html\HTMLContent;
use \UI\Forms\form;
use \UI\Presentation\gridSplitter;
use \UI\Navigation\treeView;

function buildSubTree($container, $navTree, $libName, $packageName, $name, $sub, $full_domain, $moduleID)
{
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$treeItem = $navTree->insertExpandableTreeItem($container, $full_domain, $item);
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
	$objs = sdkPackage::getNSObjects($libName, $packageName, $parentNs);	

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		/*$attr = array();
		$attr['oid'] = $key;
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		ModuleProtocol::addActionGET($item, 89, "", "", $attr);*/
		$treeItem = $navTree->insertTreeItem($container, $key, $item);
	}
}

function buildPackageTree($libName, $navTree, $navTreeElement, $moduleID)
{
	$packages = sdkLibrary::getPackageList($libName);
	foreach ($packages as $key => $value)
	{
		$item = DOM::create("div", $key);
		$treeItem = $navTree->insertExpandableTreeItem($navTreeElement, $key, $item);
		
		// Get Namespaces
		$nss = sdkPackage::getNSList($libName, $key);
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

// Platform Elements holder
$platform = DOM::create("div", "", "", "platformElements");

// Site Packages holder
$sitePackageContainer = DOM::create("div", "", "", "sitePackages");

$gsplitter = new gridSplitter();
$packageSplitter = $gsplitter->build("vertical", gridSplitter::SIDE_BOTTOM, FALSE, "Site Packages")->get();
$gsplitter->appendToMain($platform)->appendToSide($sitePackageContainer);

DOM::append($apiContainer, $packageSplitter);

// Platform Treeview
// Ebuilder api library list(!)
//$platformElementCollection = sdkLibrary::getList();
$platformElementCollection = array();
foreach ($platformElementCollection as $elem)
{
	// Platform Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->get_view();
	
	// Build Package List
	buildPackageTree($elem, $navTree, $navTreeElement, $moduleID);
}

// Site Package selector
// ___ Collect options
// ___ Create selection
$sitePackages = form::select($name = "sitePackages", $id = "", $size = "", $multiple = FALSE, $options = array());
DOM::append($sitePackageContainer, $sitePackages);

$packages = DOM::create("div", "", "", "packageCollection");
DOM::append($sitePackageContainer, $packages);

// ___ Collect Site Packages
$packageCollection = array();
// ___ Append to viewer
foreach ((array)$packageCollection as $package)
{
	$packageWrapper = DOM::create("div", "", "", "packageElements");
	// ...
	DOM::append($packagesWrapper, $packageWrapper);
	
	// Site Package Treeview
	$sitePackageElementCollection = array();
	foreach ($sitePackageElementCollection as $elem)
	{
		// Platform Tree View
		$navTree = new treeView();
		$navTreeElement = $navTree->get_view();
		
		// Build Package List
		buildPackageTree($elem, $navTree, $navTreeElement, $moduleID);
	}
}

return $htmlFragment->getReport();
//#section_end#
?>