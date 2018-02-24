<?php
//#section#[header]
// Module Declaration
$moduleID = 143;

// Inner Module Codes
$innerModules = array();
$innerModules['extensionObject'] = 142;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extension;
use \API\Resources\literals\moduleLiteral;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;
use \UI\Html\HTMLContent;

$extensionID = $_GET['id'];

$defAttr = array();
$defAttr['id'] = $extensionID;

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
$GLOBALS["actFactory"] = $actionFactory; // For extending variable scope to functions

// Create TabControl
$tabControl = new tabControl();
$tabControl->build($id = "tbr_objectListTabber", TRUE);
$objectListTabber = $tabControl->get();
$globalContainer = $HTMLContentBuilder->buildElement($objectListTabber)->get();
DOM::appendAttr($globalContainer, "class", "codeEditorSidebar");
	
$extensionManager = new extension();
$extensionManager->load($extensionID);

// Views Tab
$selected = TRUE;
$id = "View";
//$tabContent = DOM::create('div');		
	$viewsArray  = $extensionManager->getAllViews();
	if(empty($viewsArray))
	{
		$tabContent = DOM::create('span', 'Nothing');
	}
	else
	{
		//Tree View
		$navTree = new treeView();
		$tabContent = $navTree->build('', '', TRUE)->get();
	
		foreach($viewsArray as $pageObject)
		{
			$objects = listItem($pageObject);
			$attr = $defAttr;
			$attr['name'] = $pageObject;
			$actionFactory->setModuleAction($objects['item'], $moduleID, "viewEditor", "", $attr);
			
			$actionFactory->setModuleAction($objects['actionDelete'], $innerModules['extensionObject'], "deleteView", "", $attr);
			
			$treeItem = $navTree->insertItem('', $objects['wrapper']);
		}
	}
$header = moduleLiteral::get($moduleID, "lbl_views");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// PHP Script 
$selected = FALSE;
$id = "phpScript";
//$tabContent = DOM::create('div');	
	// Build Package List
	$packages = $extensionManager->getPackageList();
	if(empty($packages))
	{
		$tabContent = DOM::create('span', 'Nothing');
	}
	else
	{ 
		// Library Tree View
		$navTree = new treeView();
		$tabContent = $navTree->build('', '', TRUE)->get();
		
		foreach ($packages as $packageName => $value)
		{
			$item = DOM::create("div", $packageName);
			$treeItem = $navTree->insertExpandableTreeItem($packageName, $item, "");
			
			// Get Namespaces
			$nss = $extensionManager->getNSList($packageName);
			foreach ($nss as $nsName => $nsValue) 
				buildNsTree($navTree, $nsName, $nsValue, $packageName, "", $moduleID);
			
			// Build package children objects
			//$objs = $extensionManager->getPackageObjects($packageName);
			buildObjTree($navTree, $packageName, "", $moduleID);
		}
		
		// Clear Selected
		$selected = FALSE;
	}
$header = moduleLiteral::get($moduleID, "lbl_phpObjects");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Styles Tab
$selected = TRUE;
$id = "styles";
$tabContent = DOM::create('div');
DOM::attr($tabContent, "style", "height:100%");
	// Style
	$stylesContent = DOM::create('div');
	$header = DOM::create("span", "Styles", "", "inListHeader");
	DOM::append($stylesContent, $header);
	
	$item = DOM::create("div");
	$content = DOM::create('span', "Main");
	DOM::append($item, $content);
	$attr = $defAttr;
	$attr['type'] = 'style';
	$actionFactory->setModuleAction($item, $moduleID, "styleEditor", "", $attr);
	
	DOM::append($stylesContent, $item);
	
	DOM::append($tabContent, $stylesContent);
	// Themes
	$themesContent = DOM::create('div');
	$header = DOM::create("span", "Themes", "", "inListHeader");
	DOM::append($themesContent, $header);
	
	$themesArray = $extensionManager->getAllThemes();
	if(empty($themesArray))
	{
		$itemsList = DOM::create('span', 'Nothing');
	}
	else
	{
		//Tree View
		$navTree = new treeView();
		$itemsList = $navTree->build('', '', TRUE)->get();
	
		foreach($themesArray as $themeObject)
		{
			$objects = listItem($themeObject);
			$attr = $defAttr;
			$attr['name'] = $themeObject;
			$attr['type'] = 'theme';
			$actionFactory->setModuleAction($objects['item'], $moduleID, "styleEditor", "", $attr);
			
			$actionFactory->setModuleAction($objects['actionDelete'], $innerModules['extensionObject'], "deleteTheme", "", $attr);
			
			$treeItem = $navTree->insertItem('', $objects['wrapper']);
		}
	}
	DOM::append($themesContent, $itemsList);	
	DOM::append($tabContent, $themesContent);
$header = DOM::create('span', "Styles");//moduleLiteral::get($moduleID, "hdr_systemLayouts");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// js Script 
$selected = FALSE;
$id = "jsScript";
//$tabContent = DOM::create('div');	
	$jsScriptsArray = $extensionManager->getAllJsScripts();
	if(empty($jsScriptsArray))
	{
		$tabContent = DOM::create('span', 'Nothing');
	}
	else
	{
		//Tree View
		$navTree = new treeView();
		$tabContent = $navTree->build('', '', TRUE)->get();
	
		foreach($jsScriptsArray as $jsScriptObject)
		{
			$objects = listItem($jsScriptObject);
			$attr = $defAttr;
			$attr['name'] = $jsScriptObject;
			$actionFactory->setModuleAction($objects['item'], $moduleID, "jsScriptEditor", "", $attr);
			
			$actionFactory->setModuleAction($objects['actionDelete'], $innerModules['extensionObject'], "deleteJsScript", "", $attr);
			
			$treeItem = $navTree->insertItem('', $objects['wrapper']);				
		}
	}
$header = DOM::create('span', "Js Script");//moduleLiteral::get($moduleID, "hdr_systemLayouts");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Return output
return $HTMLContentBuilder->getReport();


function listItem($cntText)
{
	$objects = array();
	
	$elementWrapper = DOM::create("div", "", "", "itemWrapper");
		// Main Element	
		$mainElement = DOM::create("div", "", "", "listItemContent");
		DOM::append($elementWrapper, $mainElement);
		$content = DOM::create('span', $cntText);
		DOM::append($mainElement, $content);
		
		// Controls
		$controls = DOM::create("div", "", "", "listItemControls");
		DOM::append($elementWrapper, $controls);
		$deleteCtrl = DOM::create("div");
			$content = DOM::create('span', "[-]");
			DOM::append($deleteCtrl, $content);
		DOM::append($controls, $deleteCtrl);
		
	$objects['wrapper'] = $elementWrapper;
	$objects['item'] = $mainElement;
	$objects['actionDelete'] = $deleteCtrl;
	
	return $objects;
}

function buildNsTree($navTree, $name, $values, $packageName, $parentNs, $moduleID)
{
	if(!empty($parentNs))
	{
		$parentId = $parentNs;
		$itemId = $parentNs."_".$name;
	}
	else
	{
		$parentId = $packageName;
		$itemId = $name;
	}
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$navTree->insertExpandableTreeItem($itemId, $item, $parentId);	
		
	//_____ Build the query tree list
	$parentNs = $itemId;
	buildObjTree($navTree, $packageName, $parentNs, $moduleID);	
	
	if (is_array($values) & count($values) == 0)
		return;
		
	// Foreach subdomain, build a tree
	foreach ($values as $name => $value)
		buildNsTree($navTree, $name, $value, $packageName, $parentNs, $moduleID);
}
function buildObjTree($navTree, $packageName, $parentNs, $moduleID)
{	
	$extensionManager = new extension();
	$extensionManager->load($_GET['id']);
	$objs = $extensionManager->getNSObjects($packageName, $parentNs);
		
	if(!empty($parentNs))
		$parentId = $parentNs;
	else
		$parentId = $packageName;	

	foreach ($objs as $key => $value)
	{
		$item = DOM::create("div", $value['name']);
		$attr = array();		
		$attr['id'] = $_GET['id'];
		$attr['pkg'] = $packageName;
		$attr['objName'] = $value['name'];
		$attr['ns'] = $parentNs;		
		$GLOBALS["actFactory"]->setModuleAction($item, $moduleID, "objectEditor", "", $attr);
		$treeItem = $navTree->insertItem($key, $item, $parentId);
	}
}
//#section_end#
?>