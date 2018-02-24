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
importer::import("DEV", "Version");
importer::import("INU", "Views");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \INU\Views\fileExplorer;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "branchExplorer");

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

// Get vcs object
$vcs = new vcs($projectID);

// Get branch
$branch = engine::getVar("branch");
$branchName = empty($branchName) ? vcs::MASTER_BRANCH : $branchName;
$type = engine::getVar("type");

// Set fileExplorer path
$repository = $project->getRepository();
switch ($type)
{
	case "t":
		$fePath = $repository."/trunk/".$branch;
		$friendlyName = $branch." - Trunk";
		break;
	case "b":
		$fePath = $repository."/branches/".$branch;
		$friendlyName = $branch." - Branch";
		break;
	case "r":
		$fePath = $repository."/release/current/".$branch;
		$friendlyName = $branch." - Release";
		break;
	default:
		$fePath = $repository."/trunk/".$branch;
		break;
}

// Build fileExplorer
$fExplorer = new fileExplorer($fePath, "branchExplorer_".$branch."_".$projectID, $friendlyName, $showHidden = TRUE);
$branchExplorer = $fExplorer->build("", FALSE)->get();
$pageContent->append($branchExplorer);

return $pageContent->getReport();
//#section_end#
?>