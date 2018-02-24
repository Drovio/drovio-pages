<?php
//#section#[header]
// Module Declaration
$moduleID = 88;

// Inner Module Codes
$innerModules = array();
$innerModules['dashboard'] = 226;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("ESS", "Environment");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "bossHomePage", TRUE);

// Get dashboard link
$dashboardLink = HTML::select(".bossPage a.dashboard_link")->item(0);

// If there is no logged in user, remove the link to the dashboard
if (!account::validate())
	HTML::replace($dashboardLink, NULL);
else
{
	// Set action to dashboard link
	$actionFactory->setModuleAction($dashboardLink, $innerModules['dashboard']);
	
	// Hide demo
	$demo = HTML::select(".bossPage .demo")->item(0);
	HTML::replace($demo, NULL);
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($moduleID, "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$frontendPage = HTML::select(".bossPage")->item(0);
$footerMenu = module::loadView($moduleID, "footerMenu");
DOM::append($frontendPage, $footerMenu);

return $page->getReport();
//#section_end#
?>