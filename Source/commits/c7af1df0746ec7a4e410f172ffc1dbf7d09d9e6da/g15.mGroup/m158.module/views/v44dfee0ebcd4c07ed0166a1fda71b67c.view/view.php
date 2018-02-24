<?php
//#section#[header]
// Module Declaration
$moduleID = 158;

// Inner Module Codes
$innerModules = array();
$innerModules['dashboard'] = 185;
$innerModules['projectOverview'] = 186;

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
use \UI\Html\HTMLContent;

// Create Content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "developerNav");

// Build the menu
$navMenu = DOM::create("ul", "", "", "navMenu");
$pageContent->append($navMenu);

// My Dashboard Home
$header = moduleLiteral::get($moduleID, "tlb_dashboardHome");
$url = url::resolve("developer", $url = "/dashboard/", $https = FALSE, $full = FALSE);
$item = getNavMenuItem($header, $actionFactory, $innerModules['dashboard'], $url);
DOM::append($navMenu, $item);

$myProjects = project::getMyProjects(FALSE);
foreach ($myProjects as $project)
{
	// Project Overview
	$url = url::resolve("developer", $url = "/projects/index.php", $https = FALSE, $full = FALSE);
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$item = getNavMenuItem($project['title'], $actionFactory, $innerModules['projectOverview'], $url);
	DOM::append($navMenu, $item);
}


// Create toolbar nav item
function getNavMenuItem($header, $actionFactory, $moduleID, $href)
{
	// Create item
	$item = DOM::create("li", "", "", "navMenuItem");
	
	$itemContent = DOM::create("a", $header);
	DOM::attr($itemContent, "href", $href);
	DOM::attr($itemContent, "target", "_self");
	DOM::append($item, $itemContent);
	if (!is_null($moduleID))
		$actionFactory->setModuleAction($itemContent, $moduleID);
	
	return $item;
}

// Return output
return $pageContent->getReport();
//#section_end#
?>