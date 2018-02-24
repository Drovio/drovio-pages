<?php
//#section#[header]
// Module Declaration
$moduleID = 207;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesPrivileges'] = 85;

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
	$page->build("Project Not Found", "projectSecurityPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_securityTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectSecurityPage", TRUE);


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
		// Redback Core Privileges
		$privilegesModuleID = $innerModules['corePrivileges'];
		break;
	case 2:
		// Redback Modules Privileges
		$privilegesModuleID = $innerModules['modulesPrivileges'];
		break;
	case 3:
		// Redback Web Engine Core SDK Privileges
		$privilegesModuleID = $innerModules['webEnginePrivileges'];
		break;
	case 4:
		// Application Privileges Page
		$privilegesModuleID = $innerModules['appPrivileges'];
		break;
	case 5:
		// Website Privileges Page
		$privilegesModuleID = $innerModules['websitePrivileges'];
		break;
	case 6:
		// Redback Website Template Privileges
		$privilegesModuleID = $innerModules['webTemplatePrivileges'];
		break;
	case 7:
		// Redback Website Extension Privileges
		$privilegesModuleID = $innerModules['webExtensionPrivileges'];
		break;
}

// Create module container
$attr = array();
$attr['projectID'] = $projectID;
$privileges = $page->getModuleContainer($privilegesModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectSecurity");
$page->append($privileges);

// Return output
return $page->getReport();
//#section_end#
?>