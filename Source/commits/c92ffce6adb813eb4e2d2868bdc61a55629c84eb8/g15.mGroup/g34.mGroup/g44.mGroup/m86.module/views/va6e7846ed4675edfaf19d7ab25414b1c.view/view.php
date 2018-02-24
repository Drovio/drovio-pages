<?php
//#section#[header]
// Module Declaration
$moduleID = 86;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Model\units\sql\dbQuery; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formControls\formItem;
use \UI\Navigation\sideMenu;
use \UI\Presentation\frames\windowFrame;
use \SYS\Comm\db\dbConnection;

$dbc = new dbConnection();

$HTMLContent = new MContent($moduleID);
$HTMLContent->build("", "wrapper");
$actionFactory = $HTMLContent->getActionFactory();

// Sidebar
$sidebar = DOM::create("div", "", "", "sidebar");
$HTMLContent->append($sidebar);

// Set Page Content
// Developers List
//_____ Build Side Navigation Menu for Developers
$userGroupsMenu = new sideMenu();
$menuTitle = moduleLiteral::get($moduleID, "lbl_userGroupsMenuTitle");
$sideMenu = $userGroupsMenu->build("", $menuTitle)->get();
DOM::append($sidebar, $sideMenu);

$hr = DOM::create("hr");
DOM::append($sidebar, $hr);


// Create User Group Button
$formItem = new formItem();
$formItem->build("button", $name, $id, "", "uiFormButton".($positive ? " positive" : ""));
$brn_createGroup = $formItem->get(); 
$title = moduleLiteral::get($moduleID, "lbl_createUserGroup");
DOM::append($brn_createGroup, $title);

windowFrame::setAction($brn_createGroup,  $moduleID, "createUserGroup");
DOM::append($sidebar, $brn_createGroup);

// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";

// Get User Groups
$dbq = new dbQuery("999274607", "security.privileges.user");
$dbc = new dbConnection();
$result = $dbc->execute($dbq);

// Populate User Groups Navigation
while ($group = $dbc->fetch($result))
{
	$userGroupName = DOM::create("span", $group['name']);
	$userGroupItem = $userGroupsMenu->insertListItem("", $userGroupName);
	$userGroupsMenu->addNavigation($userGroupItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	// Async Action
	$attr = array();
	$attr['gid'] = $group['id'];
	$actionFactory->setModuleAction($userGroupItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
}

// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "mainContent");
$HTMLContent->append($mainContent);

// Return the report
return $HTMLContent->getReport();
//#section_end#
?>