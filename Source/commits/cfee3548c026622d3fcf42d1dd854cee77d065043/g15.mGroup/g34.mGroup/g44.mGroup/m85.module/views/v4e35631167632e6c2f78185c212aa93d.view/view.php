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
use \API\Resources\literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \UI\Html\HTMLContent;
use \UI\Navigation\sideMenu;

$dbc = new interDbConnection();

$HTMLContent = new HTMLContent();
$HTMLContent->build("", "wrapper");
$actionFactory = $HTMLContent->getActionFactory();

// Sidebar
$sidebar = DOM::create("div", "", "", "sidebar");
$HTMLContent->append($sidebar);

$devMenu = new sideMenu();

$menuTitle = moduleLiteral::get($moduleID, "lbl_userMenuTitle");
$sideMenu = $devMenu->build("", $menuTitle)->get();

// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";

// all accounts to developer group
$dbq = new dbQuery("1788859260", "security.privileges.accounts");
$users = $dbc->execute($dbq);

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

// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "mainContent");
$HTMLContent->append($mainContent);


// Return the report
return $HTMLContent->getReport();
//#section_end#
?>