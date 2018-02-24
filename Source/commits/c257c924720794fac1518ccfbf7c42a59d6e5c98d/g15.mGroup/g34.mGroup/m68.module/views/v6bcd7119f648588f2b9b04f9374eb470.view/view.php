<?php
//#section#[header]
// Module Declaration
$moduleID = 68;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Navigation\treeView;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "lbl_pageTitle", array(), FALSE);
$page->build($title, "pageManager", TRUE);

// Page Navigation
$navCollection = $page->getRibbonCollection("unitNav");
$subItem = $page->addToolbarNavItem("unitNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$newLibPkg = $panel->build("createDomain")->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_domain");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createDomain");
$title = moduleLiteral::get($moduleID, "lbl_page");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createPage");

$panel = new ribbonPanel();
$newLibPkg = $panel->build("createFolder", TRUE)->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_folder");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createFolder");
$title = moduleLiteral::get($moduleID, "lbl_delFolder");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "deleteFolder");



$sidebar = HTML::select(".uiMainSideNav")->item(0);
$main = HTML::select(".uiMainContent")->item(0);


// Create domain tree on the sidebar
$folderViewer = DOM::create("div", "", "", "folderViewer");
DOM::append($sidebar, $folderViewer);

$navTree = new treeView();
$navTreeElement = $navTree->build("pageManTree", "", TRUE)->get();
DOM::append($folderViewer, $navTreeElement);


// Init db
$dbc = new interDbConnection();

// Get all folders
$dbq = new dbQuery("737200095", "units.domains.folders");
$folders = $dbc->execute($dbq);
while ($folder = $dbc->fetch($folders))
{
	// Set Description
	$description = ($folder['is_root'] ? $folder['domain'] : $folder['name']);
	$item = DOM::create("div", $description);
	
	// Check Parent
	$treeItem = $navTree->insertExpandableTreeItem("fld.".$folder['id'], $item, "fld.".$folder['parent_id']);
	$navTree->assignSortValue($treeItem, $description);
}

// Get all pages
$dbq = new dbQuery("1339513504", "units.domains.pages");
$result = $dbc->execute($dbq);
$pages = $dbc->fetch($result, TRUE);
foreach ($pages as $pageRow)
{
	// Create tree Item
	$key = str_replace("//", "", "////file");
	$item = DOM::create("div", $pageRow[$key]);
	$attr = array();
	$attr['pageID'] = $pageRow['id'];
	$actionFactory->setModuleAction($item, $moduleID, "editPage", "#pageEditor", $attr);
	
	// Insert page
	$treeItem = $navTree->insertTreeItem("p.".$pageRow['id'], $item, "fld.".$pageRow['folder_id']);
	$navTree->assignSortValue($treeItem, $pageRow[$key]);
}



// Page editor container
$pageEditor = DOM::create("div", "", "pageEditor");
DOM::append($main, $pageEditor);

// Return Page
return $page->getReport();
//#section_end#
?>