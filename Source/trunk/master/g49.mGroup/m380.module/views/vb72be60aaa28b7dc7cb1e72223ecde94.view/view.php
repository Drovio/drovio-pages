<?php
//#section#[header]
// Module Declaration
$moduleID = 380;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;
$innerModules['devDocs'] = 289;
$innerModules['landingPage'] = 397;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\geoIP;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$pageTitle = $page->getLiteral("title", array(), FALSE);
$page->build($pageTitle, "pricingPlansPage dev-domain", TRUE, TRUE);
$sidebarContainer = HTML::select(".pricingPlans .dev-sidebar")->item(0);

// Load navigation bar on mainpage
$navBar = HTML::select(".pricingPlans .dev-mainpage .navbar")->item(0);
$navigationBar = $page->loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load sidebar
$sidebar = $page->loadView($innerModules['devHome'], "sidebar");
DOM::append($sidebarContainer, $sidebar);

// Get current country and adjust pricing symbol
$countryCode = geoIP::getCountryCode2ByIP();
if ($countryCode == "GB")
{
	// Get all symbols
	$symbols = HTML::select(".pricing-price__symbol");
	foreach ($symbols as $symElement)
		HTML::innerHTML($symElement, "£");
}

// Load footer bar on mainpage
$docContainer = HTML::select(".pricingPlans .dev-mainpage .docContainer")->item(0);
$footerBar = $page->loadView($innerModules['landingPage'], "footerBar");
DOM::append($docContainer, $footerBar);

return $page->getReport();
//#section_end#
?>