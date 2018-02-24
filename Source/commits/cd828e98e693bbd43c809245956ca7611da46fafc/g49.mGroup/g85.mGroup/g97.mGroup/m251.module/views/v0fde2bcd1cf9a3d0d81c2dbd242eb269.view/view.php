<?php
//#section#[header]
// Module Declaration
$moduleID = 251;

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
importer::import("API", "Model");
importer::import("DEV", "Modules");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Modules\modulesProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "modulesAnalysisPage", TRUE);
$whiteBox = HTML::select(".modulesAnalysis .whiteBox")->item(0);

// Set sections
$navItems = array();
$navItems["metrics"] = "modulesMetrics";
$navItems["graph"] = "";
foreach ($navItems as $class => $viewName)
{
	$ref = $class."_ref";
	$navItem = HTML::select(".modulesAnalysis .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "analysisContainer", $targetgroup = "mGroup", $navgroup = "mdan_Group", $display = "none");
	
	if (empty($viewName))
		continue;
	
	$attr = array();
	$attr['id'] = modulesProject::PROJECT_ID;
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}

// Return output
return $page->getReport();
//#section_end#
?>