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
	
// Set module actions
$navItems = array();
$navItems[] = "navigation";
$navItems[] = "errorp";
$navItems[] = "sitemap";
foreach ($navItems as $item)
{
	// Create reference id
	$ref = "pg_".$item;
	$targetgroup = "pg_target_group";
	
	// Get navitem
	$navItem = HTML::select(".pageManager .navBar .navTitle.".$item)->item(0);
	
	// Check if it is for preload
	$preload = HTML::hasClass($navItem, "selected");
	
	// Static navigation
	$page->setStaticNav($navItem, $ref, "pgmContainer", $targetgroup, "pgmNav", $display = "none");
	
	// Add Module Container
	$pgmContainer = HTML::select("#pgmContainer")->item(0);
	$mContainer = $page->getModuleContainer($innerModules[$item], "", array(), $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload);
	DOM::append($pgmContainer, $mContainer);
	
	// Set group selector
	$page->setNavigationGroup($mContainer, $targetgroup);
}


// Return output
return $page->getReport($_GET['holder']);
//#section_end#
?>