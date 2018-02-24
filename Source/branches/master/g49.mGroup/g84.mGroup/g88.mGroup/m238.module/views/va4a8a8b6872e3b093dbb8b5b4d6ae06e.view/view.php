<?php
//#section#[header]
// Module Declaration
$moduleID = 238;

// Inner Module Codes
$innerModules = array();
$innerModules['queryEditor'] = 239;

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
use \DEV\Core\sql\sqlDomain;
use \DEV\Core\coreProject;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "sqlQueryViewer")->get();

// Manager toolbar
$navBar = new navigationBar();
$navigationToolbar = $navBar->build($dock = "T", $pageContainer)->get();
$pageContent->append($navigationToolbar);

// Refresh Tool
$navTool = DOM::create("span", "", "QRefresh", "qNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "qNavTool create_new")->get();
$navBar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;

// New library
$title = moduleLiteral::get($moduleID, "lbl_createDomain");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createDomain", "", $attr);

// New package
$title = moduleLiteral::get($moduleID, "lbl_createQuery");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $moduleID, "createQuery", "", $attr);

// Create domain tree on the sidebar
$navTree = new treeView();
$navTreeElement = $navTree->build('dbQueriesTree', 'dbQueriesViewerTree', TRUE)->get();
$pageContent->append($navTreeElement);

$domains = sqlDomain::getList();
foreach ($domains as $key => $value)
	build_sub_tree($navTreeElement, $navTree, $key, $value, $key, $innerModules['queryEditor'], $actionFactory);
	
return $pageContent->getReport();




function build_sub_tree($container, $navTree, $name, $sub, $full_domain, $moduleID, $actionFactory)
{
	// Create group item container
	$item = DOM::create("div", "", "", "qDomain");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::append($item, $itemIco);
	$itemName = DOM::create("span", $name);
	DOM::append($item, $itemName);
		
	// Build the domain tree item
	$parentID = DOM::attr($container, "id");
	$treeItem = $navTree->insertExpandableTreeItem($name, $item, $parentID);
	$navTree->assignSortValue($treeItem, ".".$name);
	
	//_____ Build the query tree list
	build_q_tree($navTree, $treeItem, $full_domain, $moduleID, $actionFactory);
	
	// If there are no subdomains, exit function
	if (is_array($sub) & count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		build_sub_tree($treeItem, $navTree, $key, $value, $full_domain.".".$key, $moduleID, $actionFactory);
}

function build_q_tree($navTree, $container, $domain, $moduleID, $actionFactory)
{
	$queries = sqlDomain::getQueries($domain);
	foreach ($queries as $key => $value)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "qQuery");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", "[".$key."] ".$value);
		DOM::append($item, $itemName);

		$attr = array();
		$attr['id'] = coreProject::PROJECT_ID;
		$attr['domain'] = $domain;
		$attr['qid'] = $key;
		$actionFactory->setModuleAction($item, $moduleID, "", "", $attr, TRUE);
		
		$parentID = DOM::attr($container, "id");
		$treeItem = $navTree->insertTreeItem($key, $item, $parentID);
		$navTree->assignSortValue($treeItem, $key);
	}
}
//#section_end#
?>