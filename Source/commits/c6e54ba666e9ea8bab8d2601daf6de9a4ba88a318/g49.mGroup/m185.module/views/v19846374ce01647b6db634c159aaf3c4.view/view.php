<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

// Inner Module Codes
$innerModules = array();
$innerModules['projectDesigner'] = 187;
$innerModules['projectRepository'] = 188;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\url;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "dashboardPageContainer", TRUE);

$newProject = HTML::select("h3.newProject")->item(0);
$actionFactory->setModuleAction($newProject, $moduleID, "projectWizard");

// Get projects
$myProjects = project::getMyProjects(FALSE);
$projectList = HTML::select(".projectList")->item(0);

foreach ($myProjects as $project)
{
	// Create project box
	$projectBox = DOM::create("div", "", "", "projectRow");
	HTML::append($projectList, $projectBox);
	
	// App Controls Container
	$boxControls = DOM::create("div", "", "", "projectControls");
	DOM::append($projectBox, $boxControls);
	
	// Designer Control
	$url = url::resolve("developer", "/projects/designer.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$control = $page->getWeblink($url, "", "_self");
	DOM::attr($control, "title", "Project Designer");
	HTML::addClass($control, "boxCtrl edit");
	DOM::append($boxControls, $control);
	$actionFactory->setModuleAction($control, $innerModules['projectDesigner']);
	
	// VCS Control
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = $project['id'];
	$params['tab'] = "repository";
	$url = url::get($url, $params);
	$control = $page->getWeblink($url, "", "_self");
	DOM::attr($control, "title", "Project Repository");
	HTML::addClass($control, "boxCtrl vcs");
	DOM::append($boxControls, $control);
	$actionFactory->setModuleAction($control, $innerModules['projectRepository']);
	
	// Project title
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$projectLink = $page->getWeblink($url, $project['title'], "_blank");
	$boxTitle= DOM::create("h4", $projectLink, "", "projectTitle");
	DOM::append($projectBox, $boxTitle);
	
	// Project description
	if (!empty($project['description']))
	{
		$boxDesc = DOM::create("p", $project['description'], "", "projectDesc");
		DOM::append($projectBox, $boxDesc);
	}
}

if (count($myProjects) == 0)
{
	// noProject description
	$title = moduleLiteral::get($moduleID, "lbl_noProjects");
	$descp = DOM::create("p", $title, "", "noProjects");
	HTML::append($projectList, $descp);
	
	HTML::append($projectList, $newProject);
}

// Return output
return $page->getReport();
//#section_end#
?>