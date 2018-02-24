<?php
//#section#[header]
// Module Declaration
$moduleID = 187;

// Inner Module Codes
$innerModules = array();
$innerModules['projectHome'] = 186;
$innerModules['projectRepository'] = 188;
$innerModules['projectResources'] = 205;
$innerModules['projectPreview'] = 212;

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
importer::import("ESS", "Environment");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("", "toolbarProjectNav", TRUE);

// Set item actions
addAction("projectHome", $actionFactory);
addAction("projectRepository", $actionFactory, "repository");
addAction("projectResources", $actionFactory, "resources");
addAction("projectPreview", $actionFactory, "tester");

// Return output
return $pageContent->getReport();


// Add navigation action
function addAction($itemClass, $actionFactory, $tab = "")
{
	// Get item
	$item = HTML::select(".toolbarProjectNav .".$itemClass." a")->item(0);
	
	// Set url
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = engine::getVar("id");
	$params['name'] = engine::getVar("name");
	$params['tab'] = $tab;
	$href = url::get($url, $params);
	DOM::attr($item, "href", $href);
}
//#section_end#
?>