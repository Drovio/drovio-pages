<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;


// Create and Build Module Page
$pageContent = new MPage($moduleID);
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$pageContent->build($title, "applicationCenterPage", TRUE);
$actionFactory = $pageContent->getActionFactory();

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($moduleID, "navigationBar");
DOM::append($navBar, $navigationBar);


// Get application grid
$appContainer = HTML::select(".applicationCenterPage .appGrid")->item(0);
$view = module::loadview($moduleID, "appGridHolder");
DOM::append($appContainer, $view);

// Load footer menu
$appCenter = HTML::select(".appCenter")->item(0);
$footerMenu = module::loadView($moduleID, "footerMenu");
DOM::append($appCenter, $footerMenu);

// Return output
return $pageContent->getReport();
//#section_end#
?>