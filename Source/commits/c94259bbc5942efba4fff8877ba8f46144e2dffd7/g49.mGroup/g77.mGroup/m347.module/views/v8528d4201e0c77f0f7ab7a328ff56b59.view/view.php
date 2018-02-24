<?php
//#section#[header]
// Module Declaration
$moduleID = 347;

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
importer::import("API", "Literals");
importer::import("DEV", "Literals");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Literals\literal;

// Get project id
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

$project = new project($projectID, $projectName);
$projectInfo = $project->info();
$projectID = $projectInfo['id'];
$projectTitle = $projectInfo['title'];


// Build MContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "translationsContent", TRUE);

// Get scopes
$scopeMenu = HTML::select(".scopes")->item(0);
$pScopes = literal::getScopes($projectID);
foreach ($pScopes as $scope)
	$scopes[] = $scope['scope'];
	
asort($scopes);
foreach ($scopes as $scope)
{
	// Create scope item
	$li = DOM::create("li", $scope, "", "sc");
	DOM::append($scopeMenu, $li);
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['scope'] = $scope;
	$actionFactory->setModuleAction($li, $moduleID, "scopeLiterals", ".literalExplorer", $attr, $loading = TRUE);
}

return $pageContent->getReport();
//#section_end#
?>