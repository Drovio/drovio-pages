<?php
//#section#[header]
// Module Declaration
$moduleID = 172;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 173;
$innerModules['appEditor'] = 135;
$innerModules['sourceEditor'] = 174;

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
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \UI\Navigation\treeView;
use \UI\Presentation\tabControl;

// Create HTMLContent Object
$htmlContent = new HTMLContent();
$actionFactory = $htmlContent->getActionFactory();
$GLOBALS['moduleID'] = $moduleID;
$GLOBALS['innerModules'] = $innerModules;

// Create container
$container = DOM::create("div", "", "appSectionViewer");
$htmlContent->buildElement($container);

// Initialize Application
$appID = $_GET['appID'];
$GLOBALS['appID'] = $appID;
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	
	$errorMessage = DOM::create("h2", "Application request not valid");
	$htmlContent->append($errorMessage);
	return $htmlContent->getReport();
}
$devApp = new application($appID);

// Create TabControl for Sections
$sectionTabber = new tabControl();
$sectionTabberControl = $sectionTabber->build($id = "applicationExplorer", TRUE)->get();
DOM::append($container, $sectionTabberControl);

// Create section Navigation Bar
$navBar = new navigationBar();
$sourceNavBar = $navBar->build(navigationBar::BOTTOM, $container)->get();
DOM::append($container, $sourceNavBar);

$sectionMenu = new toolbarMenu();
$sectionMenuElement = $sectionMenu->build()->get();
$navBar->insertTool($sectionMenuElement);

// Action Attributes
$attr = array();
$attr['appID'] = $appID;


$item = $sectionMenu->insertMenuItem("View", $id = "v1");
$actionFactory->setPopupAction($item, $moduleID, "createView", $attr);

$item = $sectionMenu->insertMenuItem("Package", $id = "v2");
$actionFactory->setPopupAction($item, $moduleID, "createSrcPackage", $attr);

$item = $sectionMenu->insertMenuItem("Ns", $id = "v3");
$actionFactory->setPopupAction($item, $moduleID, "createSrcNamespace", $attr);

$item = $sectionMenu->insertMenuItem("Object", $id = "v4");
$actionFactory->setPopupAction($item, $moduleID, "createSrcObject", $attr);

$item = $sectionMenu->insertMenuItem("Style", $id = "v5");
$actionFactory->setPopupAction($item, $moduleID, "createStyle", $attr);

$item = $sectionMenu->insertMenuItem("Script", $id = "v6");
$actionFactory->setPopupAction($item, $moduleID, "createScript", $attr);

// Create Tabs

// Viewes Tab
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
	$actionFactory->setModuleAction($treeItem, $innerModules['appEditor'], "styleEditor", "", $attr);
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
	$actionFactory->setModuleAction($treeItem, $innerModules['appEditor'], "scriptEditor", "", $attr);
}

try
{
	// Application Source Tree
	$devPkg = $devApp->getSrcPackage();
	// Library Tree View
	$navTree = new treeView();
	$navTree->build("SourceTree");
	$navTreeElement = $navTree->get();
	DOM::append($appSourceContainer, $navTreeElement);
	
	buildPackageTree($devPkg, $navTree);
}
catch (Exception $ex)
{
}

function buildPackageTree($devPkg, $navTree)
{
	$packages = $devPkg->getPackages();
	foreach ($packages as $packageName => $value)
	{
		$item = DOM::create("div", $packageName);
		$navTree->insertExpandableTreeItem($packageName, $item);
		
		// Get Namespaces
		$nss = $devPkg->getNSList($packageName);
		foreach ($nss as $nsName => $nsValue)
			buildNsTree($devPkg, $navTree, $nsName, $nsValue, $packageName, "");
		
		// Build package children objects
		buildObjTree($devPkg, $navTree, $packageName, "");
	}
}

function buildNsTree($devPkg, $navTree, $nsName, $subElements, $packageName, $parentNs = "")
{
	$parentId = $parentNs;
	$itemId = $parentNs."_".$nsName;
	if (empty($parentNs)) 
	{
		$parentId = $packageName;
		$itemId = $nsName;
	}
	// Build the domain tree item
	$item = DOM::create("div", $nsName);
	$navTree->insertExpandableTreeItem($itemId, $item, $parentId);	
		
	//_____ Build the query tree list
	$parentNs = $itemId;
	buildObjTree($devPkg, $navTree, $packageName, $parentNs);
	
	if (is_array($subElements) && count($subElements) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($subElements as $name => $value)
		buildNsTree($devPkg, $navTree, $name, $value, $packageName, $parentNs);
}

function buildObjTree($devPkg, $navTree, $packageName, $parentNs = "")
{
	$objs = $devPkg->getObjects($packageName, $parentNs);
	$parentId = $parentNs;
	if (empty($parentNs))
		$parentId = $packageName;

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$treeItem = $navTree->insertTreeItem($key, $item, $parentId);
		
		// Item action
		$htmlc = new HTMLContent();
		$actionFactory = $htmlc->getActionFactory();
		$attr = array();
		$attr['appID'] = $GLOBALS['appID'];
		$attr['name'] = $key;
		$attr['package'] = $packageName;
		$attr['namespace'] = $value['namespace'];
		$actionFactory->setModuleAction($treeItem, $GLOBALS["innerModules"]['sourceEditor'], "", "", $attr);
	}
}

return $htmlContent->getReport();
//#section_end#
?>