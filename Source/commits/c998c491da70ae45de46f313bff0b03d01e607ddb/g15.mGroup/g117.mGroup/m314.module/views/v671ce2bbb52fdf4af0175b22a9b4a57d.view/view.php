<?php
//#section#[header]
// Module Declaration
$moduleID = 314;

// Inner Module Codes
$innerModules = array();
$innerModules['databases'] = 315;
$innerModules['accounts'] = 316;

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
$page->build("", "adminConfigurationPage", TRUE);

$navItems = array();
$navItems[] = "databases";
$navItems[] = "accounts";
foreach ($navItems as $item)
{
	// Create reference id
	$ref = "st_".$item;
	$targetgroup = "st_target_group";
	
	// Get navitem
	$navItem = HTML::select(".adminConfiguration .navBar .navTitle.".$item)->item(0);
	
	// Check if it is for preload
	$preload = HTML::hasClass($navItem, "selected");
	
	// Static navigation
	$page->setStaticNav($navItem, $ref, "settingsContainer", $targetgroup, "configNav", $display = "none");
	
	// Add Module Container
	$settingsContainer = HTML::select("#settingsContainer")->item(0);
	$mContainer = $page->getModuleContainer($innerModules[$item], "", array(), $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload);
	DOM::append($settingsContainer, $mContainer);
	
	// Set group selector
	$page->setNavigationGroup($mContainer, $targetgroup);
}

// Return output
return $page->getReport($_GET['holder']);
//#section_end#
?>