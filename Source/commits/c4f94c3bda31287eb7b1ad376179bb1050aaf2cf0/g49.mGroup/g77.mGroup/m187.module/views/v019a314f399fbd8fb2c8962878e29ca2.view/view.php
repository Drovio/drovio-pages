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
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("", "toolbarProjectNav", TRUE);

// Set item actions
addAction("projectHome", $actionFactory, "overview", $projectInfo);
addAction("projectRepository", $actionFactory, "repository", $projectInfo);
addAction("projectResources", $actionFactory, "resources", $projectInfo);
addAction("projectPreview", $actionFactory, "tester", $projectInfo);

// Return output
return $pageContent->getReport();


// Add navigation action
function addAction($itemClass, $actionFactory, $tab = "", $projectInfo = array())
{
	// Get project id and name
	$projectID = $projectInfo['id'];
	$projectName = $projectInfo['name'];
	
	// Get item
	$item = HTML::select(".toolbarProjectNav .".$itemClass." a")->item(0);
	
	// Set url
	if (empty($projectName))
	{
		$url = url::resolve("developer", "/projects/project.php");
		$params = array();
		$params['id'] = $projectID;
		$params['tab'] = $tab;
		$href = url::get($url, $params);
	}
	else
		$href = url::resolve("developer", "/projects/".$projectName."/".$tab."/");
	DOM::attr($item, "href", $href);
}
//#section_end#
?>