<?php
//#section#[header]
// Module Declaration
$moduleID = 338;

// Inner Module Codes
$innerModules = array();
$innerModules['navigation'] = 337;
$innerModules['errorp'] = 339;
$innerModules['robots'] = 340;
$innerModules['sitemap'] = 341;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "pageManagerPage", TRUE);
$whiteBox = HTML::select(".pageManager .whiteBox")->item(0);

// Set module actions
$navItems = array();
$navItems[] = "navigation";
$navItems[] = "errorp";
foreach ($navItems as $class)
{
	$ref = $class."_ref";
	$iModuleID = $class;
	$navItem = HTML::select(".pageManager .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "pgmContainer", $targetgroup = "mGroup", $navgroup = "adm_pg_group", $display = "none");
	
	$mContainer = $page->getModuleContainer($innerModules[$iModuleID], $viewName = "", $attr = array(), $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}

// Generate sitemap
$sitemapButton = HTML::select(".wbutton.sitemap")->item(0);
$actionFactory->setModuleAction($sitemapButton, $innerModules['sitemap']);


// Return output
return $page->getReport($_GET['holder']);
//#section_end#
?>