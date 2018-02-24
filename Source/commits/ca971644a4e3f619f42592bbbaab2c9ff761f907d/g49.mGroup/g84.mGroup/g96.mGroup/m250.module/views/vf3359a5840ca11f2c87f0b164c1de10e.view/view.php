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
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$pageContent = new MPage($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "coreAnalysisPage", TRUE);


// Navigation attributes
$targetContainer = "anpool";
$targetGroup = "anGroup";
$navGroup = "anNavGroup";

$items = array();
$items[] = "metrics";
$items[] = "graph";
$items[] = "ui";


// Core Metrics
setSection("metrics", $moduleID, "coreMetrics");

// Core Dependency Graph
setSection("graph");

// Core UI Preview
setSection("ui", $moduleID, "uiPreview");

// Return output
return $pageContent->getReport();

function setSection($name, $moduleID, $viewName)
{
	// Navigation attributes
	$targetContainer = "anpool";
	$targetGroup = "anGroup";
	$navGroup = "anNavGroup";

	// Set navigation
	$navItem = HTML::select(".mi_".$name)->item(0);
	NavigatorProtocol::staticNav($navItem, "a_".$name, $targetContainer, $targetGroup, $navGroup, $display = "none");
	
	// Container
	$sectionContainer = HTML::select(".a_".$name)->item(0);
	NavigatorProtocol::selector($sectionContainer, $targetGroup);
	if (!empty($moduleID))
	{
		$sectionContent = module::loadView($moduleID, $viewName);
		DOM::append($sectionContainer, $sectionContent);
	}
}
//#section_end#
?>