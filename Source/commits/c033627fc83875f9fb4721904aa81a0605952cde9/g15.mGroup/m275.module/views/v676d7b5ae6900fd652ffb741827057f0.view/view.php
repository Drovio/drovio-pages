<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

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
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("UI", "Content");
//#section_end#
//#section#[code]
use \API\Resources\filesystem\directory;
use \API\Resources\archive\zipManager;
use \UI\Content\HTMLFrame;
use \UI\Content\MIMEContent;
use \DEV\Projects\projectLibrary;

// Get project attributes
$projectID = engine::getVar("pid");
$projectVersion = engine::getVar("version");

// Get project release path
$releasePath = projectLibrary::getPublishedPath($projectID, $projectVersion);
$contents = directory::getContentList(systemRoot."/".$releasePath, TRUE);

// Set extended time limit
set_time_limit(600);

// Set archive name path as temp
$archiveName = "project".$projectID."_release_".$projectVersion.".zip";
$archive = sys_get_temp_dir()."/".$archiveName;
$bStatus = zipManager::create($archive, $contents, TRUE, TRUE);
if ($bStatus)
{
	// Create mimeContent to download
	$mime = new MIMEContent();
	$mime->set($archive, $type = MIMEContent::CONTENT_APP_ZIP);
	
	// Return (to download)
	return $mime->getReport($archiveName, $ignore_user_abort = FALSE, $removeFile = TRUE);
}
//#section_end#
?>