<?php
//#section#[header]
// Module Declaration
$moduleID = 139;

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
$page = new HTMLModulePage("TwoColumnsLeftSidebarFullscreen");
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$page->build($pageTitle);

$scopesWrapper = DOM::create("div", "", "scopesViewer");
$page->appendToSection("sidebar", $scopesWrapper);


// Dictionary
$headerContent = moduleLiteral::get($moduleID, "lbl_dictionary");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
DOM::append($scopesWrapper, $header);

// Create dictionary tree on the sidebar
$dictionaryTree = new treeView();
$dictionaryTreeElement = $dictionaryTree->build($id = "literalDictionary", $class = "", $sorting = FALSE)->get();
DOM::append($scopesWrapper, $dictionaryTreeElement);


// Global Literal Scopes Header
$headerContent = moduleLiteral::get($moduleID, "lbl_globalLiterals");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
DOM::append($scopesWrapper, $header);

// Create domain tree on the sidebar
$globalLitTree = new treeView();
$globalLitTreeElement = $globalLitTree->build($id = "globalLiterals", $class = "", $sorting = FALSE)->get();
DOM::append($scopesWrapper, $globalLitTreeElement);


// SDK Literal Scopes Header
$headerContent = moduleLiteral::get($moduleID, "lbl_sdkLiterals");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
DOM::append($scopesWrapper, $header);

// Create domain tree on the sidebar
$sdkLitTree = new treeView();
$sdkLitTreeElement = $sdkLitTree->build($id = "sdkLiterals", $class = "", $sorting = FALSE)->get();
DOM::append($scopesWrapper, $sdkLitTreeElement);


// Module Literal Scopes Header
$headerContent = moduleLiteral::get($moduleID, "lbl_moduleLiterals");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
DOM::append($scopesWrapper, $header);

// Create domain tree on the sidebar
$moduleLitTree = new treeView();
$moduleLitTreeElement = $moduleLitTree->build($id = "moduleLiterals", $class = "", $sorting = FALSE)->get();
DOM::append($scopesWrapper, $moduleLitTreeElement);


// Subdomain Literal Scopes Header
$headerContent = moduleLiteral::get($moduleID, "lbl_subLiterals");
$header = DOM::create("h4", "", "", "sidebarHeader");
DOM::append($header, $headerContent);
DOM::append($scopesWrapper, $header);

// Create domain tree on the sidebar
$subLitTree = new treeView();
$subLitTreeElement = $subLitTree->build($id = "subLiterals", $class = "", $sorting = FALSE)->get();
DOM::append($scopesWrapper, $subLitTreeElement);


// Get all Literal scopes
$dbc = new interDbConnection();
$dbq = new dbQuery("928581721", "resources.literals");
$result = $dbc->execute($dbq);
// Insert Scopes
while ($row = $dbc->fetch($result))
{
	$scope = $row['scope'];
	$rootScope = explode(".", $row['scope']);
	$rootScope = $rootScope[0];
	
	$item = DOM::create("span", $row['scope']);
	$treeItem;
	
	if (!empty($row['dictionary']))
	{
		$item = moduleLiteral::get($moduleID, "lbl_dictionary");
		$treeItem = $dictionaryTree->insertTreeItem("scp.".$row['scope'], $item);
	}
	else if ($rootScope == "mdl")
		$treeItem = $moduleLitTree->insertTreeItem("scp.".$row['scope'], $item);
	else if ($rootScope == "global")
		$treeItem = $globalLitTree->insertTreeItem("scp.".$row['scope'], $item);
	else if ($rootScope == "sub")
		$treeItem = $subLitTree->insertTreeItem("scp.".$row['scope'], $item);
	else
		$treeItem = $sdkLitTree->insertTreeItem("scp.".$row['scope'], $item);
	
	$attr = array();
	$attr['scope'] = $row['scope'];
	$actionFactory->setModuleAction($treeItem, $moduleID, "translationsEditor", "#literalViewer", $attr);
}


// Create Literal Viewer
$literalViewer = DOM::create("div", "", "literalViewer");
$page->appendToSection("mainContent", $literalViewer);


// Return the report
return $page->getReport();
//#section_end#
?>