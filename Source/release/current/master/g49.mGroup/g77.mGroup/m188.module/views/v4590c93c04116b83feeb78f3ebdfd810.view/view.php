<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

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
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Presentation\popups\popup;
use \DEV\Version\tools\releaseManager;
use \DEV\Projects\project;

// Get project id
$projectID = engine::getVar('id');

$project = new project($projectID);
$projectInfo = $project->info();
$projectTitle = $projectInfo['title'];


$repository = $project->getRepository($projectID);
$id = "prlm".md5("releaseManager_project_".$projectID);
$rlm = new releaseManager($id, $projectID);
$releaseControl = $rlm->build($projectTitle." | Release")->get();


// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "persistent", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");

return $vcsPopup->build($releaseControl)->getReport();
//#section_end#
?>