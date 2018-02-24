<?php
//#section#[header]
// Module Declaration
$moduleID = 90;

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
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;

$dbc = new interDbConnection();

$HTMLContent = new HTMLContent();
$HTMLContent->build("", "wrapper");
$actionFactory = $HTMLContent->getActionFactory();

// Sidebar
$sidebar = DOM::create("div", "", "", "sidebar");
$HTMLContent->append($sidebar);

// Set Page Content
// Users List
//_____ Build Side Navigation Menu for Users
$menuTitle = moduleLiteral::get($moduleID, "lbl_userMenuTitle");
DOM::append($sidebar, $menuTitle);

$sideMenu = new treeView();
$usersMenu = $sideMenu->build($id = "", $class = "", $sorting = TRUE)->get();
DOM::append($sidebar, $usersMenu);

// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";

// Get administrator accounts
$dbq = new dbQuery("389191626", "profile.account");
$personAccounts = $dbc->execute($dbq);

while ($row = $dbc->fetch($personAccounts)) 
{
	$username = (empty($row['username']) ? $row['mail'] : $row['username']);
	$itemTitle = DOM::create("span", $username);
	$userItem = $sideMenu->insertSemiExpandableTreeItem("p".$row['account_id'], $itemTitle, $parentId = "");
	$sideMenu->addNavigation($userItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['account_id'] = $row['account_id'];
	$actionFactory->setModuleAction($userItem, $moduleID, "groupsInfo", "#infoHolder", $attr);
}

// Get managed accounts
$dbq = new dbQuery("1962970958", "profile.account");
$managedAccounts = $dbc->execute($dbq);

while ($row = $dbc->fetch($managedAccounts)) 
{
	$itemTitle = DOM::create("span", $row['title']);
	$userItem = $sideMenu->insertTreeItem("p".$row['id'], $itemTitle, "p".$row['parent_id']);
	$sideMenu->addNavigation($userItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['account_id'] = $row['id'];
	$actionFactory->setModuleAction($userItem, $moduleID, "groupsInfo", "#infoHolder", $attr);
}

// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "mainContent");
$HTMLContent->append($mainContent);


// Return the report
return $HTMLContent->getReport();
//#section_end#
?>