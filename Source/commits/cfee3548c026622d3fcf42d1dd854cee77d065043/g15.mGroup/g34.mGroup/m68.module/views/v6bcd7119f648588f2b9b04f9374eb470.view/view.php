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
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \API\Resources\literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Navigation\treeView;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;

// Create Module Page
$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$page->build(moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE), "pageManager");
$actionFactory = $page->getActionFactory();

// Page Navigation
$navCollection = $page->getRibbonCollection("unitNav");
$subItem = $page->addToolbarNavItem("unitNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$newLibPkg = $panel->build("createDomain")->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_domain");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newLibItem, $moduleID, "createDomain");

$panel = new ribbonPanel();
$newLibPkg = $panel->build("createFolder")->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_folder");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newLibItem, $moduleID, "createFolder");

$panel = new ribbonPanel();
$newLibPkg = $panel->build("createPage")->get();
DOM::append($navCollection, $newLibPkg);
$title = moduleLiteral::get($moduleID, "lbl_page");
$newLibItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
ModuleProtocol::addActionGET($newLibItem, $moduleID, "createPage");



// Build Page Content
$dbc = new interDbConnection();

// Get all folders
$dbq = new dbQuery("737200095", "units.domains.folders");
$folders = $dbc->execute_query($dbq);

// Create domain tree on the sidebar
$folderViewer = DOM::create("div", "", "", "folderViewer");
$page->appendToSection("sidebar", $folderViewer);

$navTree = new treeView();
$navTreeElement = $navTree->build()->get();
DOM::append($folderViewer, $navTreeElement);

while ($folder = $dbc->fetch($folders))
{
	// Set Description
	$description = ($folder['is_root'] ? $folder['domain'] : $folder['name']);
	$item = DOM::create("div", $description);
	
	// Check Parent
	$parentItem = DOM::find("fld.".$folder['parent_id']);
	if (!is_null($parentItem))
		$treeItem = $navTree->insert_expandableTreeItem($parentItem, "fld.".$folder['id'], $item);
	else
		$treeItem = $navTree->insert_expandableTreeItem($navTreeElement, "fld.".$folder['id'], $item);
}

// Get all pages
$dbq = new dbQuery("1339513504", "units.domains.pages");
$pages = $dbc->execute_query($dbq);

while ($pageRow = $dbc->fetch($pages))
{
	// Create tree Item
	$pageName = $pageRow['file'];
	$item = DOM::create("div", $pageName);
	$attr = array();
	$attr['pageID'] = $pageRow['id'];
	ModuleProtocol::addActionGET($item, $moduleID, "editPage", "#pageEditor", $attr);
	
	// Get folder
	$pageFolder = DOM::find("fld.".$pageRow['folder_id']);
	$treeItem = $navTree->insert_treeItem($pageFolder, "p.".$pageRow['id'], $item);
}

$pageEditor = DOM::create("div", "", "pageEditor");
$page->appendToSection("mainContent", $pageEditor);

// Return Page
return $page->getReport();
//#section_end#
?>