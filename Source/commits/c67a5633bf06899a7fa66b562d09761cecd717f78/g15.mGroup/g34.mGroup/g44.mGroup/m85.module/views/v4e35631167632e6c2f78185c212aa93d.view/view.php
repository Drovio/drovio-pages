<?php
//#section#[header]
// Module Declaration
$moduleID = 85;

// Inner Module Codes
$innerModules = array();
$innerModules['userPrivileges'] = 86;
$innerModules['userGroups'] = 90;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Resources\literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\treeView;

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$HTMLModulePage->build($pageTitle);
$actionFactory = $HTMLModulePage->getActionFactory();

// Sidebar
$sidebar = DOM::create("div", "", "", "");
$HTMLModulePage->appendToSection("sidebar", $sidebar);


// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";

// Create RB_DEVELOPER tree view
$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitle_developers");


// Add RB_DEVELOPER
$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitle_developers");
$header = DOM::create("h4", $menuTitle);
DOM::append($sidebar, $header);
addTreeView($moduleID, $actionFactory, $sidebar, "devs", "RB_DEVELOPER");

$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitle_testers");
$header = DOM::create("h4", $menuTitle);
DOM::append($sidebar, $header);
addTreeView($moduleID, $actionFactory, $sidebar, "testers", "RB_TESTER");

function addTreeView($moduleID, $actionFactory, $container, $treeID, $userGroup)
{
	$tView = new treeView();
	$sideMenu = $tView->build($treeID, $class = "", $sorting = TRUE)->get();
	DOM::append($container, $sideMenu);
	
	// Get admin accounts from userGroup
	$dbc = new interDbConnection();
	$dbq = new dbQuery("1788859260", "security.privileges.accounts");
	$attr = array();
	$attr['userGroup'] = $userGroup;
	$adminDevs = $dbc->execute($dbq, $attr);
	while ($row = $dbc->fetch($adminDevs)) 
	{
		$accountName = $row['firstname']." ".$row['lastname'];
		$devName = DOM::create("span", $accountName);
		$devItem = $tView->insertSemiExpandableTreeItem("p".$row['accountID'], $devName);
		$tView->addNavigation($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
		
		$attr = array();
		$attr['aid'] = $row['accountID'];
		$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
	}
	
	
	// Get shared accounts from userGroup
	$dbq = new dbQuery("18350303667133", "security.privileges.accounts");
	$attr = array();
	$attr['userGroup'] = $userGroup;
	$managedDevs = $dbc->execute($dbq, $attr);
	while ($row = $dbc->fetch($managedDevs)) 
	{
		$devName = DOM::create("span", $row['title']);
		$devItem = $tView->insertTreeItem("p".$row['accountID'], $devName, "p".$row['parent_id']);
		$tView->addNavigation($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
		
		$attr = array();
		$attr['aid'] = $row['accountID'];
		$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
	}
}
// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "");
$HTMLModulePage->appendToSection("mainContent", $mainContent);


// Return the report
return $HTMLModulePage->getReport();
//#section_end#
?>