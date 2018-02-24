<?php
//#section#[header]
// Module Declaration
$moduleID = 90;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;

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

// Get all accounts
$dbq = module::getQuery($moduleID, "get_all_accounts");
$allAccounts = $dbc->execute($dbq);

while ($row = $dbc->fetch($allAccounts)) 
{
	$username = (empty($row['username']) ? $row['mail'] : $row['username']);
	$itemTitle = DOM::create("div", $username);
	$userItem = $sideMenu->insertSemiExpandableTreeItem("p".$row['id'], $itemTitle, $parentId = "p".$row['parent_id']);
	$sideMenu->addNavigation($userItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['account_id'] = $row['id'];
	$actionFactory->setModuleAction($itemTitle, $moduleID, "groupsInfo", ".uGroupInfoHolder", $attr);
}



// Return the report
return $pageContent->getReport();
//#section_end#
?>