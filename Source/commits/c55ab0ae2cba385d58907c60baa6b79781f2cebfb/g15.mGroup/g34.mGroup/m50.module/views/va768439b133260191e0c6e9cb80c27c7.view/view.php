<?php
//#section#[header]
// Module Declaration
$moduleID = 50;

// Inner Module Codes
$innerModules = array();
$innerModules['moduleManagement'] = 97;
$innerModules['userPrivileges'] = 86;
$innerModules['userGroups'] = 90;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

use \ESS\Protocol\client\NavigatorProtocol;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", array(), FALSE);
$page->build("Admin | ".$pageTitle, "adminSecurity", true);


// Navigation attributes
$targetcontainer = "securitySections";
$targetgroup = "securityNavGroup";
$navgroup = "securityNav";

// 
$navTitle = HTML::select(".adminSecurity .navTile.modules")->item(0);
HTML::addClass($holder, 'selected');
NavigatorProtocol::staticNav($navTitle, "moduleManagement", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// 
$navTitle = HTML::select(".adminSecurity .navTile.privileges")->item(0);
NavigatorProtocol::staticNav($navTitle, "userPrivileges", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// 
$navTitle = HTML::select(".adminSecurity .navTile.groups")->item(0);
NavigatorProtocol::staticNav($navTitle, "userGroups", $targetcontainer, $targetgroup, $navgroup, $display = "none");

//
//
$holder = HTML::select('.adminSecurity .moduleManagement')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
$module = $page->getModuleContainer($innerModules['moduleManagement'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

$holder = HTML::select('.adminSecurity .userPrivileges')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
HTML::addClass($holder, 'noDisplay');
$module = $page->getModuleContainer($innerModules['userPrivileges'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

$holder = HTML::select('.adminSecurity .userGroups')->item(0);
NavigatorProtocol::selector($holder, $targetgroup);
HTML::addClass($holder, 'noDisplay');
$module = $page->getModuleContainer($innerModules['userGroups'], "", $attr = array(), TRUE, "");
DOM::append($holder, $module);

// Return output
return $page->getReport();
//#section_end#
?>