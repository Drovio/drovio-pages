<?php
//#section#[header]
// Module Declaration
$moduleID = 93;

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
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Navigation\TreeView;

// Build Page
$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page->build($pageTitle);


// Toolbar Navigation
$navCollection = $page->getRibbonCollection("devLiterals");
$subItem = $page->addToolbarNavItem("devLiteralsSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// New Scope
$panel = new ribbonPanel();
$newScope = $panel->build("newScope")->get();
$itemTitle = moduleLiteral::get($moduleID, "lbl_newScope");
$newScopeItem = $panel->insertPanelItem($type = "small", $itemTitle, $imgURL = "", $selected = FALSE);
$actionFactory->setPopupAction($newScopeItem, $moduleID, "createNewScope");
DOM::append($navCollection, $newScope);



// Dictionary
$headerContent = moduleLiteral::get($moduleID, "lbl_dictionary");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
$page->appendToSection("sidebar", $header);

// Create dictionary tree on the sidebar
$navTree = new treeView();
$navTreeElement = $navTree->build($id = "literalDictionary", $class = "", $sorting = FALSE)->get();
$page->appendToSection("sidebar", $navTreeElement);

$item = moduleLiteral::get($moduleID, "lbl_dictionary");
$treeItem = $navTree->insertTreeItem("scp.dictionary", $item);
$attr = array();
$attr['scope'] = "global.dictionary";
$actionFactory->setModuleAction($treeItem, $moduleID, "literalEditor", "#literalViewer", $attr);

// Literal Scopes Header
$headerContent = moduleLiteral::get($moduleID, "lbl_literalScopes");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
$page->appendToSection("sidebar", $header);

// Create domain tree on the sidebar
$navTree = new treeView();
$navTreeElement = $navTree->build($id = "literalScopes", $class = "", $sorting = FALSE)->get();
$page->appendToSection("sidebar", $navTreeElement);

// Get all Literal scopes
$dbc = new interDbConnection();
$dbq = new dbQuery("251170707", "resources.literals");
$result = $dbc->execute($dbq);

// Insert Scopes
while ($row = $dbc->fetch($result))
{
	$item = DOM::create("span", $row['scope']);
	$treeItem = $navTree->insertTreeItem("scp.".$row['scope'], $item);
	
	$attr = array();
	$attr['scope'] = $row['scope'];
	$actionFactory->setModuleAction($treeItem, $moduleID, "literalEditor", "#literalViewer", $attr);
}

// Create Literal Viewer
$literalViewer = DOM::create("div", "", "literalViewer");
$page->appendToSection("mainContent", $literalViewer);

// Return the report
return $page->getReport();
//#section_end#
?>