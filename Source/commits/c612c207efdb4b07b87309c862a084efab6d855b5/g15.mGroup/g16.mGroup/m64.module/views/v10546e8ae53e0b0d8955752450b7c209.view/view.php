<?php
//#section#[header]
// Module Declaration
$moduleID = 64;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 65;

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
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\components\units\modules\module;
use \API\Developer\components\units\modules\moduleGroup;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \UI\Navigation\fileTreeView;
use \UI\Html\HTMLContent;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Initialize database connection
$dbc = new interDbConnection();

$treeView = new fileTreeView();
$moduleViewerTree = $treeView->build($id = "moduleExplorerTree", $class = "moduleViewerTree", $sorting = TRUE)->get();
$rootContainer = $pageContent->buildElement($moduleViewerTree)->get();
DOM::append($rootContainer);

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
	$folderIcon = DOM::create("span", "", "", "contentIcon mgIcon");
	DOM::append($folder, $folderIcon);
	$folderName = DOM::create("span", $row['description']);
	DOM::append($folder, $folderName);
	
	// Insert
	$folderItem = $treeView->insertExpandableTreeItem('mg'.$row['id'], $folder, $lastDepthElem[$row['depth']]);
	$treeView->assignSortValue($folderItem, $row['description']);
	
	// Group Info Item
	$span = DOM::create("div", "", "", "gInfo");
	$folderIcon = DOM::create("span", "", "", "contentIcon infoIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "Group Info");
	DOM::append($span, $spanTitle);
	$groupInfoItem = $treeView->insertTreeItem('mg'.$row['id']."info", $span, 'mg'.$row['id']);
	$treeView->assignSortValue($groupInfoItem, "AAinfo");
	
	// Set action to module info
	$attr = array();
	$attr['gid'] = $row['id'];
	$actionFactory->setModuleAction($groupInfoItem, $moduleID, "groupInfo", "", $attr);
	
	
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
	$file = DOM::create("div");
	$fileIcon = DOM::create("div", "", "", "contentIcon mIcon");
	DOM::append($file, $fileIcon);
	
	$fname = $row['title'];
	$fileName = DOM::create("span", $fname);
	DOM::append($file, $fileName);
	
	$moduleFileItem = $treeView->insertExpandableTreeItem('m'.$module_ID, $file, "mg".$row['group_id']);
	$treeView->assignSortValue($moduleFileItem, $fname);
	
	// Module Info Item
	$span = DOM::create("div", "", "", "mInfo");
	$folderIcon = DOM::create("span", "", "", "contentIcon infoIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "Module Info");
	DOM::append($span, $spanTitle);
	$moduleInfoItem = $treeView->insertTreeItem('m'.$module_ID."info", $span, 'm'.$module_ID);
	$treeView->assignSortValue($moduleInfoItem, "AAinfo");
	
	// Set action to module info
	$attr = array();
	$attr['mid'] = $module_ID;
	$actionFactory->setModuleAction($moduleInfoItem, $innerModules['viewEditor'], "moduleInfo", "", $attr);
	
	// Module Views root
	$span = DOM::create("div", "", "", "mViews");
	$folderIcon = DOM::create("span", "", "", "contentIcon vIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "Views");
	DOM::append($span, $spanTitle);
	$moduleViewsRoot = $treeView->insertExpandableTreeItem('m'.$module_ID."views", $span, 'm'.$module_ID);
	$treeView->assignSortValue($moduleViewsRoot, "Views");
	
	// Get module views
	$module = new module($module_ID);
	$views = $module->getViews();
	foreach ($views as $viewID => $viewName)
	{
		// Create view
		$fname = $viewName;
		$file = DOM::create("div");
		$fileIcon = DOM::create("div", "", "", "contentIcon auxIcon");
		DOM::append($file, $fileIcon);
		$fileName = DOM::create("span", $viewName);
		DOM::append($file, $fileName);
					
		$fileItem = $treeView->insertTreeItem("mv".$viewID, $file, 'm'.$module_ID."views");
		$treeView->assignSortValue($fileItem, $fname);
		
		$attr = array();
		$attr['mid'] = $module_ID;
		$attr['vid'] = $viewID;
		$actionFactory->setModuleAction($fileItem, $innerModules['viewEditor'], "", "", $attr);
	}
	
	/*
	// Module SQL Queries root
	$span = DOM::create("div", "", "", "mQueries");
	$folderIcon = DOM::create("span", "", "", "contentIcon mgIcon");
	DOM::append($span, $folderIcon);
	$spanTitle = DOM::create("span", "SQL Queries");
	DOM::append($span, $spanTitle);
	$moduleSQLRoot = $treeView->insertExpandableTreeItem('m'.$module_ID."queries", $span, 'm'.$module_ID);
	*/
}

return $pageContent->getReport();
//#section_end#
?>