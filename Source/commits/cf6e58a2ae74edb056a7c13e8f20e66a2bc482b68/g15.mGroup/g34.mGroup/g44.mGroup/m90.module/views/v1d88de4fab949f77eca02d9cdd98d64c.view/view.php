<?php
//#section#[header]
// Module Declaration
$moduleID = 90;

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
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Model\units\sql\dbQuery; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \SYS\Comm\db\dbConnection;

$dbc = new dbConnection();

$pageContent = new MContent($moduleID);
$pageContent->build("", "userGroupsPage", TRUE);
$actionFactory = $pageContent->getActionFactory();

// Sidebar
$sidebar = HTML::select(".userGroupsPage .sidebar .userList")->item(0);

// Set Page Content
// Users List
//_____ Build Side Navigation Menu for Users
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
	$actionFactory->setModuleAction($userItem, $moduleID, "groupsInfo", ".uGroupInfoHolder", $attr);
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
	$actionFactory->setModuleAction($userItem, $moduleID, "groupsInfo", ".uGroupInfoHolder", $attr);
}



// Return the report
return $pageContent->getReport();
//#section_end#
?>