<?php
//#section#[header]
// Module Declaration
$moduleID = 212;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesTesterPreview'] = 105;
$innerModules['coreTesterPreview'] = 213;

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
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
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
	$page->build("Project Not Found", "projectTesterPreviewPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_testerPreviewTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectTesterPreviewPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}

// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Analysis
		$analysisModuleID = $innerModules['coreTesterPreview'];
		break;
	case 2:
		// Redback Modules Analysis
		$analysisModuleID = $innerModules['modulesTesterPreview'];
		break;
	case 3:
		// Redback Web Engine Core SDK Analysis
		$analysisModuleID = $innerModules['webEngineTesterPreview'];
		break;
	case 4:
		// Application Analysis Page
		$analysisModuleID = $innerModules['appTesterPreview'];
		break;
	case 5:
		// Website Analysis Page
		$analysisModuleID = $innerModules['websiteTesterPreview'];
		break;
	case 6:
		// Redback Website Template Analysis
		$analysisModuleID = $innerModules['webTemplateTesterPreview'];
		break;
	case 7:
		// Redback Website Extension Analysis
		$analysisModuleID = $innerModules['webExtensionTesterPreview'];
		break;
}

// Create module container
$attr = array();
$attr['projectID'] = $projectID;
$privileges = $page->getModuleContainer($analysisModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectTesterPreview");
$page->append($privileges);

// Return output
return $page->getReport();
//#section_end#
?>