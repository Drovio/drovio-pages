<?php
//#section#[header]
// Module Declaration
$moduleID = 285;

// Inner Module Codes
$innerModules = array();
$innerModules['websitePageObjectEditor'] = 286;
$innerModules['wspage'] = 286;
$innerModules['spage'] = 385;

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
importer::import("API", "Literals");
importer::import("DEV", "Websites");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\Websites\website;
use \DEV\Websites\pages\wsPage;
use \DEV\Websites\pages\wsPageManager;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;

// Create application content
$mContent = new MContent();
$actionFactory = $mContent->getActionFactory();

// Get websiteID
$websiteID = $_GET['id'];

// Build content
$libraryExplorer = $mContent->build("", "pagesExplorer")->get();

// Manager toolbar
$codeMgrToolbar = new navigationBar();
$toolbar = $codeMgrToolbar->build($dock = "T", $libraryExplorer)->get();
$mContent->append($toolbar);

// Refresh Tool
$navTool = DOM::create("span", "", "libRefresh", "pgNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "pgNavTool create_new")->get();
$codeMgrToolbar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = $websiteID;
$attr['pid'] = $websiteID;

// New library
$title = moduleLiteral::get($moduleID, "hd_newFolder");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createFolder", "", $attr);

// New package
$title = moduleLiteral::get($moduleID, "hd_newPage");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createPage", "", $attr);

// New namespace
$title = moduleLiteral::get($moduleID, "hd_deleteFolder");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "deleteFolder", "", $attr);

// Create tree view
$navTree = new treeView();
$treeViewElement = $navTree->build("wsPagesLib")->get();
$mContent->append($treeViewElement);

// Read library
$pMan = new wsPageManager($websiteID);
buildLibTree($actionFactory, $innerModules, $websiteID, $navTree, $pMan);

return $mContent->getReport();


function buildLibTree($actionFactory, $innerModules, $websiteID, $navTree, $pMan, $parent = "")
{

	// Get folder docs
	$wPages = $pMan->getFolderPages($parent, $full = TRUE);
	foreach ($wPages as $pageType => $pages)
		foreach ($pages as $pageName)
		{
			$pageType = (empty($pageType) ? "wspage" : $pageType);
			
			// Create group item container
			$item = DOM::create("div", "", "", "wsp");
			$itemIco = DOM::create("span", "", "", "contentIcon flIco");
			DOM::append($item, $itemIco);
			$itemName = DOM::create("span", $pageName);
			DOM::append($item, $itemName);
			
			$itemPath = (empty($parent) ? "" : $parent."/").$pageName;
			$itemID = substr(hash("md5", $itemPath), 0, 10);
			if (!empty($parent))
				$parentID = substr(hash("md5", $parent), 0, 10);
			$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID);
			$navTree->assignSortValue($treeItem, $pageName);
			
			// Set document loader
			$attr = array();
			$attr['id'] = $websiteID;
			$attr['folder'] = $parent;
			$attr['parent'] = $parent;
			$attr['name'] = $pageName;
			$actionFactory->setModuleAction($treeItem, $innerModules[$pageType], "", "", $attr, TRUE);
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
		buildLibTree($actionFactory, $innerModules, $websiteID, $navTree, $pMan, (empty($parent) ? "" : $parent."/").$folderName);
	}
}
//#section_end#
?>