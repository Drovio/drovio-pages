<?php
//#section#[header]
// Module Declaration
$moduleID = 170;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\components\prime\indexing\libraryIndex;
use \API\Developer\components\prime\indexing\packageIndex;
use \API\Resources\filesystem\directory;
use \API\Resources\literals\moduleLiteral;
use \UI\Navigation\treeView;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\htmlComponents\weblink;

// Create Module Page
$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarFullscreen");
$GLOBALS["actFactory"] = $HTMLModulePage->getActionFactory();
$GLOBALS["moduleID"] = $moduleID;
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
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

/* Temporary Code - Start */
/* Temporary code for getting domain libraries until the creation of propers indexing */
$domain = 'SDK';
$path = '/System/Resources/Documentation/'.$domain;
$folders = directory::getContentList(systemRoot.$path, $includeHidden = FALSE, $includeDotFolders = FALSE, $relativeNames = TRUE);
$libraries = $folders['dirs'];
/* Temporary Code - End*/

foreach ($folders['dirs'] as $library)
{	
	$item = DOM::create("div", $library);
	$navTree->insertExpandableTreeItem($library, $item);
	
	// Build Package List
	buildPackageTree($navTree, $library, $library);
}

return $HTMLModulePage->getReport();

//-------------------------------------------------------------------------------------------------------------------------------------
function buildPackageTree($navTree, $libName, $parentId)
{	
	$domain = 'SDK';
	//$path = '/System/Resources/Documentation/'.$domain;
	$path = '/System/Library/'.$domain;
	//$packages = libraryIndex::getPackageList($path."/".$libName, 'index', $fullNames = TRUE);
	$objectsString = libraryIndex::getReleaseLibraryObjects($path, $libName);
	
	$packagesArray = array();
	foreach ($objectsString as $obj)
	{
		$package = explode("::", $obj, '2');
		// $package [0] ~ package name
		// $package [1] ~ package content in string form
		$packagesArray[$package[0]][] = $package[1];		
	}
	
	foreach ($packagesArray as $name => $value)
	{	
		$itemId = $name;
		
		$item = DOM::create("div", $name);
		$navTree->insertExpandableTreeItem($itemId, $item, $libName);
		buildNsTree($navTree, $value, $libName, $name, '');
	}
}

function buildNsTree($navTree, $subElements, $libName, $packageName, $parentNs)
{	
	$objectsArray = array();
	foreach ($subElements as $obj)
	{
		$temp = explode("::", $obj, '2');		
		if(count($temp) > 1)
		{
			$objectsArray[$temp[0]][] = $temp[1];
		}
		else
		{
			// Single objs
			$name = $temp[0];
			buildObjTree($navTree, $name, $libName, $packageName, $parentNs);
		}
	}
	
	foreach ($objectsArray as $name => $value)
	{	
		if(!empty($parentNs)) 
		{
			$parentId = $parentNs;
			$itemId = $parentNs."_".$name;
		}
		else
		{
			$parentId = $packageName;
			$itemId = $packageName."_".$name;
		}

		$item = DOM::create("div", $name);
		$navTree->insertExpandableTreeItem($itemId, $item, $parentId);
		
		buildNsTree($navTree, $value, $libName, $packageName, $itemId);
	}
}

function buildObjTree($navTree, $objName, $libName, $packageName, $parentNs)
{
	if(!empty($parentNs)) 
	{
		$parentId = $parentNs;
		$itemId = $parentNs."_".$objName;
	}
	else
	{
		$parentId = $packageName;
		$itemId = $packageName."_".$objName;
	}			
		
	$item = DOM::create("div", $objName);
	$attr = array();
	$attr['oid'] = $objName;
	$attr['lib'] = $libName;
	$attr['pkg'] = $packageName;
	$attr['ns'] =  str_replace($packageName."_", "", $parentNs);		
	$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["moduleID"], "objectViewer", "#docViewer", $attr);
	$treeItem = $navTree->insertTreeItem($objName, $item, $parentId); 	
}
//#section_end#
?>