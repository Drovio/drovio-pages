<?php
//#section#[header]
// Module Declaration
$moduleID = 187;

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
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Presentation\frames\windowFrame;
use \DEV\Projects\project;

// Get project id
$projectID = $_REQUEST['pid'];

$project = new project($projectID);
$projectInfo = $project->info();
$projectTitle = $projectInfo['title'];

// Create window frame
$id = "plte".md5("literalEditor_project_".$projectID);
$frame = new windowFrame($id);

// Build frame
$title = moduleLiteral::get($moduleID, "lbl_literalEditor");
$frame->build($title);

/*
// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "persistent", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");
*/
return $frame->getFrame();
//#section_end#
?>