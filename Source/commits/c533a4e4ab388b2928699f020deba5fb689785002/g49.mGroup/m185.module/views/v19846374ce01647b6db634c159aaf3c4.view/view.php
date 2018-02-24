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
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\projects\project;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "dashboardPage", TRUE);

// Page header
$text = moduleLiteral::get($moduleID, "title");
$header = HTML::select("h1.pageTitle")->item(0);
DOM::append($header, $text);

$text = moduleLiteral::get($moduleID, "lbl_newProject");
$newProject = HTML::select("h3.newProject")->item(0);
DOM::append($newProject, $text);
$actionFactory->setModuleAction($newProject, $moduleID, "projectWizard");

$text = moduleLiteral::get($moduleID, "lbl_myProjects");
$textContainer = HTML::select("h3.title")->item(0);
DOM::append($textContainer, $text);

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
	$url = url::resolve("developer", "/projects/repository.php");
	$params = array();
	$params['id'] = $project['id'];
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
	$descp = DOM::create("p", "You don't have any projects yet.", "", "noProjects");
	HTML::append($projectList, $descp);
	
	HTML::append($projectList, $newProject);
}


// Set connect containers
$dc = HTML::select(".developerNews")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_devNewsHeader");
$header = HTML::select(".developerNews h3.title")->item(0);
DOM::append($header, $title);
$p = DOM::create("p", "There are no news at the moment.");
DOM::append($dc, $p);

$dc = HTML::select(".developerNews")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_supportHeader");
$header = HTML::select(".supportLinks h3.title")->item(0);
DOM::append($header, $title);

$headerA = HTML::select(".supportLinks h4")->item(0);
$url = url::resolve("developer", "/support/");
$title = moduleLiteral::get($moduleID, "lbl_devSupport");
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($headerA, $wl);

$headerA = HTML::select(".supportLinks h4")->item(1);
$url = url::resolve("developer", "/docs/");
$title = moduleLiteral::get($moduleID, "lbl_devDocs");
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($headerA, $wl);


$dc = HTML::select(".devTools")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_devToolsHeader");
$header = HTML::select(".devTools h3.title")->item(0);
DOM::append($header, $title);

$headerA = HTML::select(".devTools h4")->item(0);
$url = url::resolve("developer", "/tools/console/");
$title = moduleLiteral::get($moduleID, "lbl_devConsole");
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($headerA, $wl);

$headerA = HTML::select(".devTools h4")->item(1);
$url = url::resolve("developer", "/api/");
$title = moduleLiteral::get($moduleID, "lbl_devPublicAPI");
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($headerA, $wl);

$headerA = HTML::select(".devTools h4")->item(2);
$url = url::resolve("developer", "/tools/status/");
$title = moduleLiteral::get($moduleID, "lbl_devPlatformStatus");
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($headerA, $wl);

// Return output
return $page->getReport();
//#section_end#
?>