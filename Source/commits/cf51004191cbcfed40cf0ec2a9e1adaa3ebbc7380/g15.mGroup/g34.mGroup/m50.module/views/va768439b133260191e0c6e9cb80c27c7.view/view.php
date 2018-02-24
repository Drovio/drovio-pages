<?php
//#section#[header]
// Module Declaration
$moduleID = 50;

// Inner Module Codes
$innerModules = array();
$innerModules['modules'] = 97;
$innerModules['explorer'] = 184;
$innerModules['accounts'] = 342;
$innerModules['privileges'] = 343;

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
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "securityManagerPage", TRUE);
	
// Set module actions
$navs = array();
$navs[] = "modules";
$navs[] = "privileges";
$navs[] = "explorer";
$navs[] = "accounts";
foreach ($navs as $navigationItem)
{
	// Set static navigation
	$navItem = HTML::select(".securityManager .navBar .navTitle.".$navigationItem)->item(0);
	NavigatorProtocol::staticNav($navItem, "", "", "", "secNav", $display = "none");
	
	// Check reference module
	if (!isset($innerModules[$navigationItem]))
		continue;
	
	// Add module action to item
	$actionFactory->setModuleAction($navItem, $innerModules[$navigationItem], "", ".securityManager .editorPanes", array(), $loading = TRUE);
}


// Load the default Item
$configPanes = HTML::select('.securityManagerPage .editorPanes')->item(0);
$moduleView = module::loadView($innerModules['modules']);
DOM::append($configPanes, $moduleView);


// Return output
return $pageContent->getReport();



/*
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", array(), FALSE);
$pageContent->build("Admin | ".$pageTitle, "adminSecurity", true);


// Navigation attributes
$targetcontainer = "securitySections";
$targetgroup = "securityNavGroup";
$navgroup = "securityNav";

// 
$navTitle = HTML::select(".adminSecurity .navTile.modules")->item(0);
HTML::addClass($navTitle, 'selected');
NavigatorProtocol::staticNav($navTitle, "moduleManagement", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// 
$navTitle = HTML::select(".adminSecurity .navTile.privileges")->item(0);
NavigatorProtocol::staticNav($navTitle, "userPrivileges", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// 
$navTitle = HTML::select(".adminSecurity .navTile.groups")->item(0);
NavigatorProtocol::staticNav($navTitle, "userGroups", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// 
$navTitle = HTML::select(".adminSecurity .navTile.explorer")->item(0);
NavigatorProtocol::staticNav($navTitle, "pageExplorer", $targetcontainer, $targetgroup, $navgroup, $display = "none");

//
//
$holder = HTML::select('.adminSecurity .moduleManagement')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
$module = $pageContent->getModuleContainer($innerModules['moduleManagement'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

$holder = HTML::select('.adminSecurity .userPrivileges')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
$module = $pageContent->getModuleContainer($innerModules['userPrivileges'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

$holder = HTML::select('.adminSecurity .userGroups')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
$module = $pageContent->getModuleContainer($innerModules['userGroups'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

$holder = HTML::select('.adminSecurity .pageExplorer')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
$module = $pageContent->getModuleContainer($innerModules['pageExplorer'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

// Return output
return $pageContent->getReport();
*/
//#section_end#
?>