<?php
//#section#[header]
// Module Declaration
$moduleID = 205;

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
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("INU", "Views");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;
use \DEV\Projects\project;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectResourcesPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_resourcesTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectResourcesPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}


// Create a file explorer
$explorer = new fileExplorer($project->getResourcesFolder(), "projectResources", $projectTitle." Resources");
$projectResourcesExplorer = $explorer->build()->get();

$resourceContainer = HTML::select(".resourceContainer")->item(0);
DOM::append($resourceContainer, $projectResourcesExplorer);

// Return output
return $page->getReport();
//#section_end#
?>