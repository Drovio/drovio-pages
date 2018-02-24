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

// Create Module Page
$page = new HTMLModulePage("simpleOneColumnCenter");
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "dashboardPage");


$title = moduleLiteral::get($moduleID, "lbl_myProjects");
$header = DOM::create("h2", $title);
$page->appendToSection("mainContent", $header);


// Get projects
$myProjects = project::getMyProjects(FALSE);

foreach ($myProjects as $project)
{
	// Create project box
	$projectBox = DOM::create("div", "", "", "projectBox");
	$page->appendToSection("mainContent", $projectBox);
	
	// App Controls Container
	$boxControls = DOM::create("div", "", "", "boxControls");
	DOM::append($projectBox, $boxControls);
	
	// Designer Control
	$control = DOM::create("a", "", "", "boxCtrl edit");
	$url = url::resolve("developer", "/projects/designer.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($boxControls, $control);
	$actionFactory->setModuleAction($control, $innerModules['projectDesigner']);
	
	// VCS Control
	$control = DOM::create("a", "", "", "boxCtrl vcs");
	$url = url::resolve("developer", "/projects/repository.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($boxControls, $control);
	$actionFactory->setModuleAction($control, $innerModules['projectRepository']);
	
	// Project title
	$boxTitle= DOM::create("h3", $project['title'], "", "boxTitle");
	DOM::append($projectBox, $boxTitle);
	
	// Project description
	$boxDesc = DOM::create("p", $project['description'], "", "boxDesc");
	DOM::append($projectBox, $boxDesc);
}


// Return output
return $page->getReport();
//#section_end#
?>