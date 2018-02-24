<?php
//#section#[header]
// Module Declaration
$moduleID = 56;

// Inner Module Codes
$innerModules = array();
$innerModules['appEditor'] = 132;
$innerModules['sdkObjectEditor'] = 89;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
use \API\Developer\components\sdk\sdkObject;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \UI\Presentation\tabControl;
use \INU\Developer\documentation\classDocumentor;

$GLOBALS["sdkObjectEditor"] = $innerModules['sdkObjectEditor'];

$pageContent = new HTMLContent();
$pageContent->build("", "sdkPackageViewer");
$GLOBALS["actFactory"] = $pageContent->getActionFactory();


// Create TabControl for API and UI
$libraryTabber = new tabControl();
$libraryTabber->build($id = "tbr_libraryBrowser", TRUE);
$libraryTabberControl = $libraryTabber->get();

$pageContent->append($libraryTabberControl);

// Get all libraries
$libraries = sdkLibrary::getList();
$selected = TRUE;

foreach ($libraries as $library)
{
	// Library Tree View
	$navTree = new treeView();
	$navTree->build($library."Tree");
	$navTreeElement = $navTree->get();

	// Build Package List
	buildPackageTree($navTree, $library);

	$libraryTabber->insertTab($library, $library, $navTreeElement, $selected);	
	
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
	$packages = sdkLibrary::getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		$item = DOM::create("div", $packageName);
		$navTree->insertExpandableTreeItem($packageName, $item);
		
		// Get Namespaces
		$nss = sdkPackage::getNSList($libName, $packageName);
		
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($navTree, $nsName, $nsValue, $libName, $packageName, "");
		
		// Build package children objects
		buildObjTree($navTree, $libName, $packageName, "");
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
	// Build the domain tree item
	$item = DOM::create("div", $nsName);
	$navTree->insertExpandableTreeItem($itemId, $item, $parentId);	
		
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
	$objs = sdkPackage::getNSObjects($libName, $packageName, $parentNs);
	
	if(!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['oid'] = $key;
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["sdkObjectEditor"], "", "", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		
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