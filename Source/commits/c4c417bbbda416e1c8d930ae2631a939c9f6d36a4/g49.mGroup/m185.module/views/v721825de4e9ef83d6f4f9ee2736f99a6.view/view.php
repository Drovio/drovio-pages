<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

// Inner Module Codes
$innerModules = array();
$innerModules['projectOverview'] = 186;

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
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\url;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "projectExplorerContainer", TRUE);

// Page header
$newProject = HTML::select("h3.newProject")->item(0);
$actionFactory->setModuleAction($newProject, $moduleID, "projectWizard");

// Get projects
$myProjects = project::getMyProjects(FALSE);
$projectList = HTML::select(".projectList")->item(0);

if (count($myProjects) > 0)
{
	$descp = HTML::select(".noProjects")->item(0);
	HTML::replace($descp, null);
}
foreach ($myProjects as $project)
{
	// Create project box
	$projectBox = DOM::create("div", "", "", "projectRow");
	DOM::append($projectList, $projectBox);
	
	// Project link
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$projectLink = $pageContent->getWeblink($url, "", "_blank");
	DOM::append($projectBox, $projectLink);
	
	$attr = array();
	$attr['pdata'] = TRUE;
	$actionFactory->setModuleAction($projectLink, $innerModules['projectOverview'], "", ".devDashboardMain", $attr);
	
	// Project Ico
	$boxIco = DOM::create("span", "", "", "projectIco empty");
	DOM::append($projectLink, $boxIco);
	
	// Project title
	$boxTitle= DOM::create("h4", $project['title'], "", "projectTitle");
	DOM::append($projectLink, $boxTitle);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>