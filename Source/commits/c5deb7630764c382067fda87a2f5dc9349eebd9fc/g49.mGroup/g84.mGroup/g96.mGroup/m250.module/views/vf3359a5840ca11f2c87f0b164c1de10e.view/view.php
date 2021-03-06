<?php
//#section#[header]
// Module Declaration
$moduleID = 250;

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
importer::import("DEV", "Core");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Core\coreProject;

// Create Module Page
$pageContent = new MPage($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "coreAnalysisPage", TRUE);

// Set sections
$navItems = array();
$navItems["metrics"] = "coreMetrics";
$navItems["graph"] = "";
$navItems["uimodel"] = "uiPreview";
foreach ($navItems as $item => $itemData)
{
	// Create reference id
	$ref = "st_".$item;
	$targetgroup = "st_target_group";
	
	// Get navitem
	$navItem = HTML::select(".coreAnalysis .navBar .navTitle.".$item)->item(0);
	
	// Check if it is for preload
	$preload = HTML::hasClass($navItem, "selected");
	
	// Static navigation
	NavigatorProtocol::staticNav($navItem, $ref, "analysisContainer", $targetgroup, "stNav", $display = "none");
	
	if (empty($itemData))
		continue;
	
	// Set attributes
	$attr = array();
	$attr['id'] = coreProject::PROJECT_ID;
	
	// Add Module Container
	$analysisContainer = HTML::select("#analysisContainer")->item(0);
	$mContainer = $pageContent->getModuleContainer($moduleID, $itemData, $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload);
	DOM::append($analysisContainer, $mContainer);
	
	// Set group selector
	NavigatorProtocol::selector($mContainer, $targetgroup);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>