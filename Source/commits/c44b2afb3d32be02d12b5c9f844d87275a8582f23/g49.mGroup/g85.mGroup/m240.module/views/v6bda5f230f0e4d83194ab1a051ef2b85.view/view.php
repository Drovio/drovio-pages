<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 241;
$innerModules['queryEditor'] = 242;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Profile\account;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Modules\MContent;
use \DEV\Modules\module;
use \DEV\Modules\moduleGroup;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "moduleExplorer")->get();

// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "mRefresh", "mNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Initialize database connection
$dbc = new dbConnection();

$treeView = new treeView();
$moduleViewerTree = $treeView->build($id = "moduleExplorerTree", $class = "moduleViewerTree", $sorting = TRUE)->get();
$pageContent->append($moduleViewerTree);

// Get Module Groups
$dbq = new dbQuery("677677266", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$moduleGroups = $dbc->execute($dbq, $attr);

// Module Groups
$lastDepthElem = array();
$lastDepthElem[] = "";
while ($row = $dbc->fetch($moduleGroups))
{
	// Create group item container
	$folder = DOM::create("div", "", "", "mGroup");
	$folderIcon = DOM::create("span", "", "", "contentIcon libIco");
	DOM::append($folder, $folderIcon);
	$folderName = DOM::create("span", $row['description']);
	DOM::append($folder, $folderName);
	
	// Insert
	$folderItem = $treeView->insertExpandableTreeItem('mg'.$row['id'], $folder, $lastDepthElem[$row['depth']]);
	$treeView->assignSortValue($folderItem, ".".$row['description']);
	
	// Group Info Item
	$span = DOM::create("div", "", "", "gInfo");
	$folderIcon = DOM::create("span", "", "", "contentIcon infoIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "Group Info");
	DOM::append($span, $spanTitle);
	$groupInfoItem = $treeView->insertTreeItem('mg'.$row['id']."info", $span, 'mg'.$row['id']);
	$treeView->assignSortValue($groupInfoItem, "..info");
	
	// Set action to module info
	$attr = array();
	$attr['gid'] = $row['id'];
	$actionFactory->setModuleAction($groupInfoItem, $moduleID, "groupInfo", "", $attr, TRUE);
	
	
	// Set last depth element for children
	$lastDepthElem[$row['depth']+1] = 'mg'.$row['id'];
}

// Get Developer Modules
$dbq = new dbQuery("564007386", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$modules = $dbc->execute($dbq, $attr);
while ($row = $dbc->fetch($modules)) 
{
	// Get moduleID
	$module_ID = $row['id'];
	
	// Module Info Item
	$f = DOM::create("div");
	$fileIcon = DOM::create("div", "", "", "contentIcon pkgIco");
	DOM::append($f, $fileIcon);
	
	$fname = $row['title'];
	$fileName = DOM::create("span", $fname);
	DOM::append($f, $fileName);
	
	$moduleFileItem = $treeView->insertExpandableTreeItem('m'.$module_ID, $f, "mg".$row['group_id']);
	$treeView->assignSortValue($moduleFileItem, $fname);
	
	// Module Info Item
	$span = DOM::create("div", "", "", "mInfo");
	$folderIcon = DOM::create("span", "", "", "contentIcon infoIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "Module Info");
	DOM::append($span, $spanTitle);
	$moduleInfoItem = $treeView->insertTreeItem('m'.$module_ID."info", $span, 'm'.$module_ID);
	$treeView->assignSortValue($moduleInfoItem, ".info");
	
	// Set action to module info
	$attr = array();
	$attr['mid'] = $module_ID;
	$actionFactory->setModuleAction($moduleInfoItem, $moduleID, "moduleInfo", "", $attr, TRUE);
	
	// Get module views
	$module = new module($module_ID);
	$views = $module->getViews();
	if (count($views) > 0)
	{
		// Module Views root
		$span = DOM::create("div", "", "", "mViews");
		$folderIcon = DOM::create("span", "", "", "contentIcon fldIco");
		DOM::append($span, $folderIcon);
		$spanTitle = DOM::create("span", "Views");
		DOM::append($span, $spanTitle);
		$moduleViewsRoot = $treeView->insertExpandableTreeItem('m'.$module_ID."views", $span, 'm'.$module_ID);
		$treeView->assignSortValue($moduleViewsRoot, "AViews");
		
		// Fetch views
		foreach ($views as $viewID => $viewName)
		{
			// Create view
			$f = DOM::create("div");
			$fileIcon = DOM::create("div", "", "", "contentIcon flIco");
			DOM::append($f, $fileIcon);
			$fileName = DOM::create("span", $viewName);
			DOM::append($f, $fileName);
						
			$fileItem = $treeView->insertTreeItem("mv".$viewID, $f, 'm'.$module_ID."views");
			$treeView->assignSortValue($fileItem, $viewName);
			
			$attr = array();
			$attr['mid'] = $module_ID;
			$attr['vid'] = $viewID;
			$actionFactory->setModuleAction($fileItem, $innerModules['viewEditor'], "", "", $attr, TRUE);
		}
	}
	
	// Get module queries
	$queries = $module->getQueries();
	if (count($queries) > 0)
	{
		// Module SQL Queries root
		$span = DOM::create("div", "", "", "mQueries");
		$folderIcon = DOM::create("span", "", "", "contentIcon fldIco");
		DOM::append($span, $folderIcon);
		$spanTitle = DOM::create("span", "Queries");
		DOM::append($span, $spanTitle);
		$moduleSQLRoot = $treeView->insertExpandableTreeItem('m'.$module_ID."queries", $span, 'm'.$module_ID);
		$treeView->assignSortValue($moduleSQLRoot, "BQueries");
		
		// Fetch queries
		foreach ($queries as $queryID => $queryName)
		{
			// Create view
			$f = DOM::create("div");
			$fileIcon = DOM::create("div", "", "", "contentIcon flIco");
			DOM::append($f, $fileIcon);
			$fileName = DOM::create("span", $queryName);
			DOM::append($f, $fileName);
						
			$fileItem = $treeView->insertTreeItem("mq".$queryID, $f, 'm'.$module_ID."queries");
			$treeView->assignSortValue($fileItem, $queryName);
			
			$attr = array();
			$attr['mid'] = $module_ID;
			$attr['qid'] = $queryID;
			$actionFactory->setModuleAction($fileItem, $innerModules['queryEditor'], "", "", $attr, TRUE);
		}
	}
}

return $pageContent->getReport();
//#section_end#
?>