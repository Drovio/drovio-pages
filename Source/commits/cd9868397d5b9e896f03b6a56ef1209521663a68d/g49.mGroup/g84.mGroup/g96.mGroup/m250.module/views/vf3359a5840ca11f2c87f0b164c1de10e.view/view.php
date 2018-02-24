<?php
//#section#[header]
// Module Declaration
$moduleID = 250;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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

// Console container
$graphContainer = HTML::select(".a_graph")->item(0);
//$consoleContent = module::loadView($moduleID, "console");
//DOM::append($consoleContainer, $consoleContent);
NavigatorProtocol::selector($graphContainer, $targetGroup);

// Load history logs
$uiPreviewContainer = HTML::select(".a_ui")->item(0);
$previewContent = module::loadView($moduleID, "uiPreview");
DOM::append($uiPreviewContainer, $previewContent);
NavigatorProtocol::selector($uiPreviewContainer, $targetGroup);


// Set navigation
$mi_graph = HTML::select(".mi_graph")->item(0);
NavigatorProtocol::staticNav($mi_graph, "a_graph", $targetContainer, $targetGroup, $navGroup, $display = "none");

$mi_ui = HTML::select(".mi_ui")->item(0);
NavigatorProtocol::staticNav($mi_ui, "a_ui", $targetContainer, $targetGroup, $navGroup, $display = "none");

// Return output
return $pageContent->getReport();
//#section_end#
?>