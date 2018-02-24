<?php
//#section#[header]
// Module Declaration
$moduleID = 295;

// Inner Module Codes
$innerModules = array();
$innerModules['open'] = 345;
$innerModules['manifests'] = 344;

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
importer::import("DEV", "Core");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Core\coreProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "coreSecurityPage", TRUE);
$whiteBox = HTML::select(".coreSecurity .whiteBox")->item(0);

$navItems = array();
$navItems[] = "open";
$navItems[] = "manifests";
foreach ($navItems as $class)
{
	$ref = $class."_ref";
	$iModuleID = $class;
	$navItem = HTML::select(".coreSecurity .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "securityContainer", $targetgroup = "mGroup", $navgroup = "mGroup", $display = "none");
	
	if (!isset($innerModules[$iModuleID]))
		continue;
	
	$attr = array();
	$attr['id'] = coreProject::PROJECT_ID;
	$mContainer = $page->getModuleContainer($innerModules[$iModuleID], $viewName = "", $attr, $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}

// Return output
return $page->getReport();
//#section_end#
?>