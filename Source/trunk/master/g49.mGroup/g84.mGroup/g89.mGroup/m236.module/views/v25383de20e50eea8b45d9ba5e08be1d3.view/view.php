<?php
//#section#[header]
// Module Declaration
$moduleID = 236;

// Inner Module Codes
$innerModules = array();
$innerModules['pageEditor'] = 237;

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
importer::import("DEV", "Core");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \DEV\Core\ajax\ajaxDirectory;
use \DEV\Core\coreProject;

// Build and Return HTML Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "ajaxPageViewer")->get();


// Manager toolbar
$navBar = new navigationBar();
$navigationToolbar = $navBar->build($dock = "T", $pageContainer)->get();
$pageContent->append($navigationToolbar);

// Refresh Tool
$navTool = DOM::create("span", "", "ajaxRefresh", "ajaxNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "ajaxNavTool create_new")->get();
$navBar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;

// New library
$title = moduleLiteral::get($moduleID, "hdr_createNewDirectory");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createDirectory", "", $attr);

// New package
$title = moduleLiteral::get($moduleID, "hdr_createNewPage");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createPage", "", $attr);


// Create domain tree on the sidebar
$treeView = new treeView();
$navTreeElement = $treeView->build("ajaxPageExplorer", "", TRUE)->get();
$pageContent->append($navTreeElement);

$dirs = ajaxDirectory::getDirectories();
foreach ($dirs as $key => $value)
	buildSubTree($navTreeElement, $treeView, $key, $value, $key, $innerModules['pageEditor'], $actionFactory);
	
// Build the root pages	
buildPages($treeView, $navTreeElement, "", $innerModules['pageEditor'], $actionFactory);


return $pageContent->getReport();



function buildSubTree($container, $treeView, $name, $sub, $fullDirectory, $moduleID, $actionFactory)
{
	if (empty($fullDirectory))
		return;
	
	// Create group item container
	$item = DOM::create("div", "", "", "ajxDir");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $name);
	DOM::append($item, $itemName);
		
	// Build the domain tree item
	$parentID = DOM::attr($container, "id");
	$treeItem = $treeView->insertExpandableTreeItem($name, $item, $parentID);
	$treeView->assignSortValue($treeItem, ".".$name);
	
	// Build the query tree list
	buildPages($treeView, $treeItem, $fullDirectory, $moduleID, $actionFactory);
	
	// If there are no subdomains, exit function
	if (is_array($sub) && count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		buildSubTree($treeItem, $treeView, $key, $value, $fullDirectory."/".$key, $moduleID, $actionFactory);
}

function buildPages($treeView, $container, $directory, $moduleID, $actionFactory)
{
	$pages = ajaxDirectory::getPages($directory);
	foreach ($pages as $pageName)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "ajxPage");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $pageName.".php");
		DOM::append($item, $itemName);
	
		$attr = array();
		$attr['id'] = coreProject::PROJECT_ID;
		$attr['d'] = $directory;
		$attr['name'] = $pageName;
		$actionFactory->setModuleAction($item, $moduleID, "", "", $attr, TRUE);
		
		$parentID = DOM::attr($container, "id");
		$treeItem = $treeView->insertTreeItem($key, $item, $parentID);
		$treeView->assignSortValue($treeItem, $pageName);
	}
}
//#section_end#
?>