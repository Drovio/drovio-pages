<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

// Inner Module Codes
$innerModules = array();
$innerModules['objectEditor'] = 174;
$innerModules['viewEditor'] = 173;

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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \UI\Navigation\treeView;
use \UI\Presentation\tabControl;
use \DEV\Apps\application;
use \DEV\Apps\components\source\sourceLibrary;
use \DEV\Apps\components\source\sourcePackage;

// Create HTMLContent Object
$htmlContent = new HTMLContent();
$actionFactory = $htmlContent->getActionFactory();
$GLOBALS['actFactory'] = $actionFactory;
$GLOBALS['moduleID'] = $moduleID;
$GLOBALS['innerModules'] = $innerModules;

// Create container
$container = DOM::create("div", "", "appSectionViewer");
$htmlContent->buildElement($container);

// Initialize Application
$appID = $_GET['appID'];
$devApp = new application($appID);

// Create TabControl for Sections
$sectionTabber = new tabControl();
$sectionTabberControl = $sectionTabber->build($id = "applicationExplorer", TRUE)->get();
DOM::append($container, $sectionTabberControl);

// Create Tabs

// Views Tab
$tabHeader = "Views";
$appViewsContainer = DOM::create("div", "");
$sectionTabber->insertTab("appViews", $tabHeader, $appViewsContainer, $selected = TRUE);

// Source Page Tab
$tabHeader = "Source";
$appSourceContainer = DOM::create("div", "", "", "sourceCode");
$sectionTabber->insertTab("appSource", $tabHeader, $appSourceContainer, $selected = FALSE);

// Scripts Tab
$tabHeader = "Scripts";
$appScriptsContainer = DOM::create("div", "");
$sectionTabber->insertTab("appScripts", $tabHeader, $appScriptsContainer, $selected = FALSE);

// Styles Tab
$tabHeader = "Styles";
$appStylesContainer = DOM::create("div", "");
$sectionTabber->insertTab("appStyles", $tabHeader, $appStylesContainer, $selected = FALSE);

// Application Views Tree
$navTree = new treeView();
$navTree->build("AppViewerTree");
$navTreeElement = $navTree->get();
DOM::append($appViewsContainer, $navTreeElement);

// Get views
$views = $devApp->getViews();
foreach ($views as $view)
{
	$item = DOM::create("div", $view);
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $view;
	$treeItem = $navTree->insertTreeItem("v".$view, $item, $parentId = "");
	$actionFactory->setModuleAction($treeItem, $innerModules['viewEditor'], "", "", $attr);
}

// Application Styles Tree
$navTree = new treeView();
$navTree->build("appStylesTree");
$navTreeElement = $navTree->get();
DOM::append($appStylesContainer, $navTreeElement);

$styles = $devApp->getStyles();
foreach ($styles as $style)
{
	$item = DOM::create("div", $style.".css");
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $style;
	$treeItem = $navTree->insertTreeItem("s".$style, $item, $parentId = "");
	$actionFactory->setModuleAction($treeItem, $moduleID, "styleEditor", "", $attr);
}

// Application Scripts Tree
$navTree = new treeView();
$navTree->build("appScriptsTree");
$navTreeElement = $navTree->get();
DOM::append($appScriptsContainer, $navTreeElement);

$scripts = $devApp->getScripts();
foreach ($scripts as $script)
{
	$item = DOM::create("div", $script.".js");
	$attr = array();
	$attr['appID'] = $appID;
	$attr['name'] = $script;
	$treeItem = $navTree->insertTreeItem("s".$script, $item, $parentId = "");
	$actionFactory->setModuleAction($treeItem, $moduleID, "scriptEditor", "", $attr);
}


// Get all libraries
$navTree = new treeView();
$navTree->build("appSourceTree");
$navTreeElement = $navTree->get();
DOM::append($appSourceContainer, $navTreeElement);

$sdkLibrary = new sourceLibrary($appID);
$libraries = $sdkLibrary->getList();
foreach ($libraries as $library)
{
	// Library Tree View
	$item = DOM::create("div", $library);
	$navTree->insertExpandableTreeItem($library, $item);

	// Build Package List
	buildPackageTree($appID, $navTree, $library);
}


return $htmlContent->getReport();


function buildPackageTree($appID, $navTree, $libName)
{
	$sdkLibrary = new sourceLibrary($appID);
	$packages = $sdkLibrary->getPackageList($libName);
	foreach ($packages as $packageName => $value)
	{
		$item = DOM::create("div", $packageName);
		$navTree->insertExpandableTreeItem($packageName, $item, $libName);
		
		// Get Namespaces
		$sdkp = new sourcePackage($appID);
		$nss = $sdkp->getNSList($libName, $packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($appID, $navTree, $nsName, $nsValue, $libName, $packageName, "");
		
		buildObjTree($appID, $navTree, $libName, $packageName, $parentNs);
	}
}

function buildNsTree($appID, $navTree, $nsName, $subElements, $libName, $packageName, $parentNs)
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
	buildObjTree($appID, $navTree, $libName, $packageName, $parentNs);	
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($appID, $navTree, $name, $value, $libName, $packageName, $parentNs);
}

function buildObjTree($appID, $navTree, $libName, $packageName, $parentNs)
{
	$sdkp = new sourcePackage($appID);
	$objs = $sdkp->getObjects($libName, $packageName, $parentNs);
	if (!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		if ($value['namespace'] != str_replace("_", "::", $parentNs))
			continue;
			
		$item = DOM::create("div", $value['name']);
		$attr = array();
		$attr['appID'] = $appID;
		$attr['lib'] = $libName;
		$attr['pkg'] = $packageName;
		$attr['ns'] = $parentNs;
		$attr['oid'] = $value['name'];
		$GLOBALS["actFactory"]->setModuleAction($item, $GLOBALS["innerModules"]["objectEditor"], "", "", $attr);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
	}
}
//#section_end#
?>