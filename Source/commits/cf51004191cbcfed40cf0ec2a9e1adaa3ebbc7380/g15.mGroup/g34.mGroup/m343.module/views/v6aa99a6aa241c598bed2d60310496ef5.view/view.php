<?php
//#section#[header]
// Module Declaration
$moduleID = 343;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formFactory;
use \UI\Navigation\sideMenu;

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "accGroupPrivilegesPage", TRUE);

$gList = HTML::select(".accGroupPrivileges .gList")->item(0);
// User Groups list
$userGroupsMenu = new sideMenu();
$sideMenu = $userGroupsMenu->build('', '')->get();
DOM::append($gList, $sideMenu);

// Create new group action
$newGroup = HTML::select(".accGroupPrivileges .ng")->item(0);
$actionFactory->setModuleAction($newGroup,  $moduleID, "createUserGroup");

// Get User Groups
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_account_groups");
$result = $dbc->execute($dbq);

// Populate User Groups Navigation
while ($group = $dbc->fetch($result))
{
	$userGroupName = DOM::create("span", $group['name']);
	$userGroupItem = $userGroupsMenu->insertListItem("", $userGroupName);
	$userGroupsMenu->addNavigation($userGroupItem, "", "", "", "gNavGroup", $nav_display);
	
	// Async Action
	$attr = array();
	$attr['gid'] = $group['id'];
	$actionFactory->setModuleAction($userGroupItem, $moduleID, "groupPrivilegesEditor", ".accGroupPrivilegesPage .groupInfoHolder", $attr, $loading = TRUE);
}

// Return the report
return $pageContent->getReport();
//#section_end#
?>