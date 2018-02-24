<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Presentation");
importer::import("DEV", "Version");
//#section_end#
//#section#[code]
use \API\Developer\projects\project;
use \UI\Presentation\popups\popup;
use \DEV\Version\releaseManager;

// Get project id
$projectID = $_REQUEST['pid'];

$projectInfo = project::info($projectID);
$projectTitle = $projectInfo['title'];


$repository = project::getRepository($projectID);
$id = "prlm".md5("releaseManager_project_".$projectID);
$rlm = new releaseManager($id, $repository, FALSE);
$releaseControl = $rlm->build($projectTitle." | Release")->get();


// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "persistent", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");

return $vcsPopup->build($releaseControl)->getReport();
//#section_end#
?>