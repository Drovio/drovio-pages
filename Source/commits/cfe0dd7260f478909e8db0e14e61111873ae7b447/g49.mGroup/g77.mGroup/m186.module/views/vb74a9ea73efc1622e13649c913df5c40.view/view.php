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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\popups\popup;
use \DEV\Projects\project;

// Build the popup
$popup = new popup();
$popup->type($type = "persistent", $toggle = FALSE);
$popup->background(TRUE);
$popup->position("user");


// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Add notification
	
	// Return report
	return $popup->build($designer)->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle." | Designer", "projectDesignerPage");


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $popup->build($designer)->getReport();
}

// Toolbar Navigation
$vcsItem = $page->addToolbarNavItem("commitManager", $title = "Commit", $class = "vcs");
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setPopupAction($vcsItem, $moduleID, "commitManager", $attr);


// Get project type and load designer
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Designer
		$publisherModuleID = $innerModules['corePublisher'];
		break;
	case 2:
		// Redback Modules Designer
		$publisherModuleID = $innerModules['modulesPublisher'];
		break;
	case 3:
		// Redback Web Engine Core SDK
		$publisherModuleID = $innerModules['webEnginePublisher'];
		break;
	case 6:
		// Redback Website Template Designer
		$publisherModuleID = $innerModules['webTemplatePublisher'];
		break;
	case 7:
		// Redback Website Extension Designer
		$publisherModuleID = $innerModules['webExtensionPublisher'];
		break;
	case 8:
		// Redback Application Designer
		$publisherModuleID = $innerModules['appEnginePublisher'];
		break;
}

// Create module container
$designer = HTMLContent::getModuleContainer($publisherModuleID, $action = "", $attr = array(), $startup = TRUE, $containerID = "projectDesigner");


return $popup->build($designer)->getReport();
//#section_end#
?>