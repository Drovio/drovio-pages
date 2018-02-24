<?php
//#section#[header]
// Module Declaration
$moduleID = 266;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 269;

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
importer::import("API", "Resources");
importer::import("DEV", "Apps");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\treeView;
use \DEV\Apps\application;
use \DEV\Apps\views\appViewManager;

// Create page content Object
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize Application
$appID = engine::getVar('id');
$devApp = new application($appID);

// Build page
$pageContainer = $pageContent->build("", "viewExplorer", TRUE)->get();


// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "VRefresh", "vNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);


// Application Views Tree
$navTree = new treeView();
$navTreeElement = $navTree->build("appViewsViewer")->get();
$pageContent->append($navTreeElement);

// Read library
$vMan = new appViewManager($appID);
buildLibTree($actionFactory, $innerModules['viewEditor'], $appID, $navTree, $vMan);


return $pageContent->getReport();


function buildLibTree($actionFactory, $moduleID, $appID, $navTree, $pMan, $parent = "")
{

	// Get folder docs
	$views = $pMan->getFolderViews($parent);
	foreach ($views as $viewName)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "wsp");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $viewName.".view");
		DOM::append($item, $itemName);
		
		$itemPath = (empty($parent) ? "" : $parent."/").$viewName;
		$itemID = substr(hash("md5", $itemPath), 0, 10);
		if (!empty($parent))
			$parentID = substr(hash("md5", $parent), 0, 10);
		$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID);
		$navTree->assignSortValue($treeItem, $viewName);
		
		// Set document loader
		$attr = array();
		$attr['id'] = $appID;
		$attr['parent'] = $parent;
		$attr['name'] = $viewName;
		$actionFactory->setModuleAction($treeItem, $moduleID, "", "", $attr, TRUE);
	}
	
	
	// Get sub folders
	$folders = $pMan->getFolders($parent);
	foreach ($folders as $folderName => $children)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "wsf");
		$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $folderName);
		DOM::append($item, $itemName);
		
		$itemPath = (empty($parent) ? "" : $parent."/").$folderName;
		$itemID = substr(hash("md5", $itemPath), 0, 10);
		if (!empty($parent))
			$parentID = substr(hash("md5", $parent), 0, 10);
		$treeItem = $navTree->insertExpandableTreeItem($itemID, $item, $parentID);
		$navTree->assignSortValue($treeItem, ".".$folderName);
		
		// Get children folders
		buildLibTree($actionFactory, $moduleID, $appID, $navTree, $pMan, (empty($parent) ? "" : $parent."/").$folderName);
	}
}
//#section_end#
?>