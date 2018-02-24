<?php
//#section#[header]
// Module Declaration
$moduleID = 207;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesPrivileges'] = 257;

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
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\url;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_securityTitle", array(), FALSE);
$page->build($ovTitle." | ".$projectTitle, "projectSecurityPage", TRUE);


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
	// Remove uc container
	$ucDiv = HTML::select("div.uc")->item(0);
	HTML::replace($ucDiv, NULL);
	
	// Create module container
	$attr = array();
	$attr['projectID'] = $projectID;
	$privileges = $page->getModuleContainer($privilegesModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectSecurity");
	
	// Replace main content
	$uiMainContent = HTML::select(".projectSecurityPage .uiMainContent")->item(0);
	HTML::replace($uiMainContent, $privileges);
}
else
{
	$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
	$header = HTML::select("h1.title")->item(0);
	DOM::append($header, $title);
	
	$title = moduleLiteral::get($moduleID, "lbl_pageDescription");
	$header = HTML::select("h3.description")->item(0);
	DOM::append($header, $title);
	
	$title = moduleLiteral::get($moduleID, "lbl_pageUc");
	$header = HTML::select("h3.uc")->item(0);
	DOM::append($header, $title);
}

// Return output
return $page->getReport();
//#section_end#
?>