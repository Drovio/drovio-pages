<?php
//#section#[header]
// Module Declaration
$moduleID = 86;

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
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formFactory;
use \UI\Navigation\sideMenu;

$dbc = new dbConnection();

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "userPrivilegesPage", TRUE);

$gList = HTML::select(".userPrivileges .gListWrapper .gList")->item(0);
// User Groups list
$userGroupsMenu = new sideMenu();
$sideMenu = $userGroupsMenu->build('', '')->get();
DOM::append($gList, $sideMenu);


$ng = HTML::select(".userPrivileges .ng")->item(0);
// Create User Group Button
$title = moduleLiteral::get($moduleID, "lbl_createUserGroup");
$f = new formFactory();
$btn_createGroup = $f->getButton($title, $name, $class);
DOM::append($ng, $btn_createGroup);
$actionFactory->setModuleAction($btn_createGroup,  $moduleID, "createUserGroup");



// Static Navigation Attributes
$nav_ref = "infoHolder";
$nav_targetcontainer = "infoHolder";
$nav_targetgroup = "infoHolder";
$nav_navgroup = "infoHolder";

// Get User Groups
$dbq = module::getQuery($moduleID, "get_user_groups");
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

// Return the report
return $pageContent->getReport();
//#section_end#
?>