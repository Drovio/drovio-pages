<?php
//#section#[header]
// Module Declaration
$moduleID = 169;

// Inner Module Codes
$innerModules = array();
$innerModules['appCenter'] = 92;

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
use \UI\Modules\MPage;

$appID = engine::getVar('id');
$appName = engine::getVar('name');

// Create and Build Module Page
$page = new MPage($moduleID);
$title = $page->getLiteral("title", array(), FALSE);
$page->build($title, "applicationPlayerPage", TRUE, TRUE);
$actionFactory = $page->getActionFactory();

// Load navigation bar
$navBar = HTML::select(".applicationPlayer .navBar")->item(0);
$navigationBar = $page->loadView($innerModules['appCenter'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load side bar
$sideBarContainer = HTML::select(".applicationPlayer .sidebar")->item(0);
$sideBar = $page->loadView($innerModules['appCenter'], "sideBar");
DOM::append($sideBarContainer, $sideBar);

// Load application player
$appCenterContainer = HTML::select(".applicationPlayer .appCenterContentHolder")->item(0);
$attr = array();
$attr['id'] = $appID;
$attr['name'] = $appName;
$appPlayer = $page->getModuleContainer($moduleID, $viewName = "playApp", $attr, $startup = TRUE, $containerID = "applicationPlayerContainer", $loading = TRUE, $preload = FALSE);
DOM::append($appCenterContainer, $appPlayer);

// Return output
return $page->getReport();
//#section_end#
?>