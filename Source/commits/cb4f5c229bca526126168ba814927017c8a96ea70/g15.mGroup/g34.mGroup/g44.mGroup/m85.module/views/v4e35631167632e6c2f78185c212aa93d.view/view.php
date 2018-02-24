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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\treeView;
use \DEV\Projects\project;

$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$HTMLModulePage->build();
$actionFactory = $HTMLModulePage->getActionFactory();

// Sidebar
$sidebar = DOM::create("div", "", "", "");
$HTMLModulePage->appendToSection("sidebar", $sidebar);


// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";


// Project Accounts
$menuTitle = moduleLiteral::get($moduleID, "lbl_menuTitle_projectAccounts");
$header = DOM::create("h4", $menuTitle);
DOM::append($sidebar, $header);
$project = new project($_GET['projectID'], $_GET['projectTitle']);
$projectAccounts = $project->getProjectAccounts();


$tView = new treeView();
$sideMenu = $tView->build("ModulePrivileges", $class = "", $sorting = TRUE)->get();
DOM::append($sidebar, $sideMenu);

foreach ($projectAccounts as $pAccount)
{
	$accountName = $pAccount['administrator'] ? $pAccount['firstname']." ".$pAccount['lastname'] : $pAccount['title'];
	$devName = DOM::create("span", $accountName);
	$devItem = $tView->insertSemiExpandableTreeItem("p".$pAccount['accountID'], $devName);
	$tView->addNavigation($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	
	$attr = array();
	$attr['aid'] = $pAccount['accountID'];
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#infoHolder", $attr);
}

// Main Content
$mainContent = DOM::create("div", "", "infoHolder", "");
$HTMLModulePage->appendToSection("mainContent", $mainContent);


// Return the report
return $HTMLModulePage->getReport();
//#section_end#
?>