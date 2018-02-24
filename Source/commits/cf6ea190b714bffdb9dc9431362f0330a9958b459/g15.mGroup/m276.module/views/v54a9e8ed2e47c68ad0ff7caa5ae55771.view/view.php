<?php
//#section#[header]
// Module Declaration
$moduleID = 276;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Core");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \UI\Navigation\treeView;
use \UI\Modules\MPage;
use \UI\Core\components\ribbon\rPanel;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "pageManager", TRUE);

// Page Navigation
$navCollection = $page->getRibbonCollection("unitNav");
$subItem = $page->addToolbarNavItem("unitNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

$panel = new rPanel();
$newLibPkg = $panel->build("createDomain")->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_newDomain");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createDomain");
$title = moduleLiteral::get($moduleID, "lbl_newPage");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createPage");

$panel = new rPanel();
$newLibPkg = $panel->build("createFolder", TRUE)->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_newFolder");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "createFolder");
$title = moduleLiteral::get($moduleID, "lbl_delFolder");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$actionFactory->setModuleAction($newLibItem, $moduleID, "deleteFolder");



$sidebar = HTML::select(".pageManager .uiMainSideNav")->item(0);
$main = HTML::select(".pageManager .uiMainContent")->item(0);


// Create domain tree on the sidebar
$folderViewer = DOM::create("div", "", "", "folderViewer");
DOM::append($sidebar, $folderViewer);

$navTree = new treeView();
$navTreeElement = $navTree->build("pageManTree", "", TRUE)->get();
DOM::append($folderViewer, $navTreeElement);


// Init db
$dbc = new dbConnection();

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
	$item = DOM::create("div", $pageRow["file"]);
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