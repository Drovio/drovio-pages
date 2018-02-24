<?php
//#section#[header]
// Module Declaration
$moduleID = 337;

// Inner Module Codes
$innerModules = array();

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
importer::import("ESS", "Protocol");
importer::import("SYS", "Resources");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \SYS\Resources\pages\domain;
use \SYS\Resources\pages\pageFolder;
use \SYS\Resources\pages\page;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "pageExplorerContainer", TRUE);


// Set page explorer toolbar
$navBar = new navigationBar();
$topNav = $navBar->build(navigationBar::TOP, $pageContent->get())->get();
$pageContent->append($topNav);

// Refresh servers
$navTool = DOM::create("span", "", "pgRefresh", "pgNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Add new server
$navTool = DOM::create("span", "", "", "pgNavTool add_new");
$navBar->insertToolbarItem($navTool);
$actionFactory->setModuleAction($navTool, $moduleID, "createDomain", "", array(), $loading = TRUE);

// Get all domains
$domains = domain::getAllDomains();
$pageExplorer = HTML::select(".pageExplorer")->item(0);
foreach ($domains as $domain)
{
	// Set attributes
	$attr = array();
	$attr['fid'] = $domain['id'];
	
	// Create domain icon
	$showTitle = moduleLiteral::get($moduleID, "lbl_exploreFolder");
	$domainItem = getPGItem($domain['domain_name'], "domain", $domain['id'], $actionFactory, $moduleID, "folderView", $showTitle, $attr);
	DOM::append($pageExplorer, $domainItem);
}

// Return output
return $pageContent->getReport();

function getPGItem($name, $class, $folderID = NULL, $actionFactory, $moduleID, $actionView, $showTitle, $attr)
{
	// Build item
	$item = buildItem($name, $class, $actionFactory, $moduleID, $actionView, $showTitle, $attr);
	
	// Return item if no folder given
	if (empty($folderID))
		return $item;
	
	// Add folders and pages
	$folders = pageFolder::getSubFolders($folderID);
	$pages = page::getFolderPages($folderID);
	if (count($folders) > 0 || count($pages) > 0)
	{
		// Create sublist
		$itemSubList = DOM::create("ul", "", "", "subList");
		DOM::append($item, $itemSubList);
		
		// Append folders
		foreach ($folders as $folder)
		{
			// Skip root folders
			if ($folder['is_root'])
				continue;
			
			// Set attributes
			$attr = array();
			$attr['fid'] = $folder['id'];
				
			// Get item again and append to sublist
			$showTitle = moduleLiteral::get($moduleID, "lbl_exploreFolder");
			$fItem = getPGItem($folder['name'], "folder", $folder['id'], $actionFactory, $moduleID, "folderView", $showTitle, $attr);
			DOM::append($itemSubList, $fItem);
		}
		
		// Append pages
		foreach ($pages as $page)
		{
			// Set attributes
			$attr = array();
			$attr['pid'] = $page['id'];
			
			// Get item and append to sublist
			$showTitle = moduleLiteral::get($moduleID, "lbl_editPage");
			$pItem = getPGItem($page['file'], "page", NULL, $actionFactory, $moduleID, "pageEditor", $showTitle, $attr);
			DOM::append($itemSubList, $pItem);
		}
	}
	
	// Return item
	return $item;
}

function buildItem($name, $class, $actionFactory, $moduleID, $actionView, $showTitle, $attr = array())
{
	// Create item content
	$itemContent = DOM::create("div", "", "", "pgItemContent");
	
	// Add icon and title
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($itemContent, $ico);
	
	$show = DOM::create("div", $showTitle, "", "show");
	DOM::append($itemContent, $show);
	$actionFactory->setModuleAction($show, $moduleID, $actionView, ".pageManager .contentEditor", $attr, $loading = TRUE);
	
	$title = DOM::create("div", $name, "", "title");
	DOM::append($itemContent, $title);
	
	// Static navigation attributes
	NavigatorProtocol::staticNav($itemContent, "", "", "", "peNav", $display = "none");
	
	// Create item and append class
	$item = DOM::create("li", $itemContent, "", "pgItem");
	HTML::addClass($item, $class);
	
	// Return item
	return $item;
}
//#section_end#
?>