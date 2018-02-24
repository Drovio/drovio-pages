<?php
//#section#[header]
// Module Declaration
$moduleID = 95;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \DEV\Core\ajax\ajaxDirectory;

// Build and Return HTML Content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "ajaxPageViewer");


$ajaxControlToolbar = DOM::create("div", "", "", "ajaxControlToolbar");
$pageContent->append($ajaxControlToolbar);

$title = moduleLiteral::get($moduleID, "lbl_refresh");
$refreshBtn = DOM::create("div", $title, "refreshAjax", "toolbarTool");
DOM::append($ajaxControlToolbar, $refreshBtn);


// Create domain tree on the sidebar
$treeView = new treeView();
$navTreeElement = $treeView->build("ajaxPageExplorer", "", TRUE)->get();
$pageContent->append($navTreeElement);

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
	$treeView->assignSortValue($treeItem, $name);
	
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
		$attr['d'] = $directory;
		$attr['name'] = $pageName;
		$actionFactory->setModuleAction($item, $moduleID, "ajaxPageEditor", "", $attr);
		
		$parentID = DOM::attr($container, "id");
		$treeItem = $treeView->insertTreeItem($key, $item, $parentID);
		$treeView->assignSortValue($treeItem, $pageName);
	}
}

$dirs = ajaxDirectory::getDirectories();
foreach ($dirs as $key => $value)
	buildSubTree($navTreeElement, $treeView, $key, $value, $key, $moduleID, $actionFactory);
	
// Build the root pages	
buildPages($treeView, $navTreeElement, "", $moduleID, $actionFactory);


return $pageContent->getReport();
//#section_end#
?>