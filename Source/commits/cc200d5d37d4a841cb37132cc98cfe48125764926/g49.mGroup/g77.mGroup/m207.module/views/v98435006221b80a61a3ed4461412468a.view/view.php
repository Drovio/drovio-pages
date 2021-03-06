<?php
//#section#[header]
// Module Declaration
$moduleID = 207;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesPrivileges'] = 257;
$innerModules['corePrivileges'] = 295;
$innerModules['appPrivileges'] = 348;

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
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

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

if (!empty($privilegesModuleID))
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectSecurityPage");
	
	// Append module container
	$attr = array();
	$attr['id'] = $projectID;
	$marketSettings = $page->getModuleContainer($privilegesModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectSecuritySettingsContainer", $loading = FALSE, $preload = TRUE);
	$page->append($marketSettings);
}
else
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
	$page->build($title." | ".$projectTitle, "projectSecurityPage", TRUE);
}

// Return output
return $page->getReport();
//#section_end#
?>