<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;

// Build Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build page content
$pageContent->build("", "dev-sidebar-container", TRUE);
$sidebar = HTML::select(".dev-sidebar-container .sidebar")->item(0);

// Get all menus and set static nav
$menuItems = HTML::select(".dev-sidebar-container .menu .menu-item a");
foreach ($menuItems as $menuItem)
	$pageContent->setStaticNav($menuItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "sdgroup", $display = "none");


// Load doc menu
$docMenu = $pageContent->loadView($moduleID, "docMenu");
DOM::append($sidebar, $docMenu);

// Load sdk reference menu
$docMenu = $pageContent->loadView($moduleID, "sdkMenu");
DOM::append($sidebar, $docMenu);

return $pageContent->getReport();
//#section_end#
?>