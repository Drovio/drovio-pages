<?php
//#section#[header]
// Module Declaration
$moduleID = 50;

// Inner Module Codes
$innerModules = array();
$innerModules['modules'] = 97;
$innerModules['explorer'] = 184;
$innerModules['accounts'] = 342;
$innerModules['privileges'] = 343;

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
$page->build("", "securityManagerPage", TRUE);
$whiteBox = HTML::select(".securityManager .whiteBox")->item(0);

// Set module actions
$navItems = array();
$navItems[] = "modules";
$navItems[] = "privileges";
$navItems[] = "explorer";
$navItems[] = "accounts";
foreach ($navItems as $class)
{
	$ref = $class."_ref";
	$iModuleID = $class;
	$navItem = HTML::select(".securityManager .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "scmContainer", $targetgroup = "mGroup", $navgroup = "adm_sc_group", $display = "none");
	
	$mContainer = $page->getModuleContainer($innerModules[$iModuleID], $viewName = "", $attr = array(), $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}


// Return output
return $page->getReport($_GET['holder']);
//#section_end#
?>