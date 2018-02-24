<?php
//#section#[header]
// Module Declaration
$moduleID = 358;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create and Build Module Page
$page = new MPage($moduleID);
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "applicationInfoContainerPage", TRUE, TRUE);
$actionFactory = $page->getActionFactory();

// Load navigation bar
$navBar = HTML::select(".applicationInfoContainer .navBar")->item(0);
$navigationBar = module::loadView($innerModules['appCenter'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load side bar
$sideBarContainer = HTML::select(".applicationInfoContainer .sidebar")->item(0);
$sideBar = module::loadView($innerModules['appCenter'], "sideBar");
DOM::append($sideBarContainer, $sideBar);

// Load application info
$appInfoViewerContainer = HTML::select(".applicationInfoContainer .appCenterContentHolder")->item(0);
$applicationList = module::loadView($moduleID, "applicationInfoViewer");
DOM::append($appInfoViewerContainer, $applicationList);

// Return output
return $page->getReport();
//#section_end#
?>