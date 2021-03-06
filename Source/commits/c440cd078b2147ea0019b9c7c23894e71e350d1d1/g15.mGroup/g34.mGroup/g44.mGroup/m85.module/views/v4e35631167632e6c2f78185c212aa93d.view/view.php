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
use \UI\Navigation\sideMenu;

$dbc = new interDbConnection();

$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
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


// all person accounts to developer group 
$dbq = new dbQuery("1788859260", "security.privileges.accounts");
$attr = array();
$attr['userGroup'] = 'RB_developer';
$users = $dbc->execute($dbq, $attr); 

$devMenu = new sideMenu();

$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitleAdminAccounts");
$sideMenu = $devMenu->build("", $menuTitle)->get();

while ($row = $dbc->fetch($users)) 
{
	$devName = DOM::create("span", $row['username']." [aid = ".$row['accountID']."]");
	$devItem = $devMenu->insertListItem("", $devName);
	$devMenu->addNavigation($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['dev_id'] = $row['accountID'];
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
}
DOM::append($sidebar, $sideMenu);

// all managed accounts to developer group 
$dbq = new dbQuery("18350303667133", "security.privileges.accounts");
$attr = array();
$attr['userGroup'] = 'RB_developer';
$users = $dbc->execute($dbq, $attr);

$devMenu = new sideMenu();

$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitleSharedAccounts");
$sideMenu = $devMenu->build("", $menuTitle)->get();

while ($row = $dbc->fetch($users)) 
{
	$devName = DOM::create("span", $row['title']." [aid = ".$row['accountID']."]");
	$devItem = $devMenu->insertListItem("", $devName);
	$devMenu->addNavigation($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['dev_id'] = $row['accountID'];
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
}
DOM::append($sidebar, $sideMenu);

// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "");
$HTMLModulePage->appendToSection("mainContent", $mainContent);


// Return the report
return $HTMLModulePage->getReport();
//#section_end#
?>