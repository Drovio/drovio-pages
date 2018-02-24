<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Projects\projectLibrary;
use \DEV\Projects\projectReadme;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "projectReadMe");

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Check if project is open
if (!$projectInfo['public'])
{
	// Create error notification
	
	// Return output
	return $pageContent->getReport();
}

// Create readme container
$readmeContainer = DOM::create("div", "", "", "readmeContainer");
$pageContent->append($readmeContainer);

// Get published folder
$version = projectLibrary::getLastProjectVersion($projectID);
$publishedFolder = projectLibrary::getPublishedPath($projectID, $version);

// Load document
$projectReadme = new projectReadme($publishedFolder, TRUE);
$readmeContent = $projectReadme->load();
HTML::innerHTML($readmeContainer, $readmeContent);

// Return output
return $pageContent->getReport();
//#section_end#
?>