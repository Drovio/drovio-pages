<?php
//#section#[header]
// Module Declaration
$moduleID = 299;

// Inner Module Codes
$innerModules = array();
$innerModules['webHome'] = 94;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "webMarketPage", TRUE);
$actionFactory = $page->getActionFactory();


// Add footer
$bossMarket = HTML::select(".webMarket")->item(0);
$marketFooter = module::loadView($innerModules['webHome'], "footerMenu");
DOM::append($bossMarket, $marketFooter);

return $page->getReport();
//#section_end#
?>