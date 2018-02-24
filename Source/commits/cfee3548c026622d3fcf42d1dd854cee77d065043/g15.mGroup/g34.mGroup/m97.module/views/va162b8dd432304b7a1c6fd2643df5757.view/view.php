<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \UI\HTML\HTMLModulePage;
use \UI\Navigation\treeView;

// Build the Module Page
$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$actionFactory = $page->getActionFactory();
$page->build("Module Security", "moduleSecurity");


$navTree = new treeView();
$moduleGroupTree = $navTree->build()->get();
$page->appendToSection("sidebar", $moduleGroupTree);

// Get Module Groups
$dbc = new interDbConnection();
$dbq = new dbQuery("547558037", "units.groups");
$result = $dbc->execute_query($dbq, $attr);
$moduleGroups = $dbc->toFullArray($result);

$lastDepthElem = array();
$lastDepthElem[0] = $moduleGroupTree;
foreach ($moduleGroups as $group)
{
	$groupName = DOM::create("div", $group['description']);
	$groupItem = $navTree->insert_semiExpandableTreeItem($lastDepthElem[$group['depth']], 'mg_'.$group['id'], $groupName);
	$attr = array();
	$attr['gid'] = $group['id'];
	$actionFactory->setModuleAction($groupItem, $moduleID, "moduleViewer", "#moduleViewer", $attr);
	$navTree->add_sortValue($groupItem, $group['description']);
	
	$lastDepthElem[$group['depth']+1] = $groupItem;
}


// Create module Viewer
$moduleViewer = DOM::create("div", "", "moduleViewer");
$page->appendToSection("mainContent", $moduleViewer);


return $page->getReport();
//#section_end#
?>