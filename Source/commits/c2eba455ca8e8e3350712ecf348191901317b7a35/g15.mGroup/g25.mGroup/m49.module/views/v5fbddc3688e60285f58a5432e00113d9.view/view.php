<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Resources\DOMParser;
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \DEV\Core\sql\sqlDomain;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("sqlQueryViewer", "queryViewer");

$queryControlToolbar = DOM::create("div", "", "", "queryControlToolbar");
$pageContent->append($queryControlToolbar);

$title = moduleLiteral::get($moduleID, "lbl_refresh");
$refreshBtn = DOM::create("div", $title, "refreshQueries", "toolbarTool");
DOM::append($queryControlToolbar, $refreshBtn);

// Create domain tree on the sidebar
$navTree = new treeView();
$navTreeElement = $navTree->build('dbQueriesTree', 'dbQueriesViewerTree', TRUE)->get();
$pageContent->append($navTreeElement);


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
	$navTree->assignSortValue($treeItem, $name);
	
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
		$attr['domain'] = $domain;
		$attr['qid'] = $key;
		$actionFactory->setModuleAction($item, $moduleID, "queryEditor", "", $attr);
		
		$parentID = DOM::attr($container, "id");
		$treeItem = $navTree->insertTreeItem($key, $item, $parentID);
		$navTree->assignSortValue($treeItem, $value);
	}
}

$domains = sqlDomain::getList();
foreach ($domains as $key => $value)
	build_sub_tree($navTreeElement, $navTree, $key, $value, $key, $moduleID, $actionFactory);
	
return $pageContent->getReport();
//#section_end#
?>