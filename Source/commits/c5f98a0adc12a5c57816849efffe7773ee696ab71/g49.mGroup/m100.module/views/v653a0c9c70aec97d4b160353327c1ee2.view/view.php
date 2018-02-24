<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['dashboard'] = 185;
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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "developerNav", TRUE);

// Build the menu
$navMenu = HTML::select(".navMenu")->item(0);

// My Dashboard Home
$item = HTML::select(".devDashboard a")->item(0);
$actionFactory->setModuleAction($item, $innerModules['dashboard']);


// List projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$myProjects = $dbc->fetch($result, TRUE);

// Get projects organized by team
$teamProjects = array();
foreach ($myProjects as $project)
	$teamProjects[$project['teamName']][] = $project;
	
foreach ($teamProjects as $teamName => $projects)
{
	// Set team
	$item = DOM::create("li", $teamName, "", "navMenuItem team");
	DOM::append($navMenu, $item);
	
	// Set team projects
	foreach ($projects as $project)
	{
		// Project Overview
		$url = url::resolve("developer", $url = "/projects/project.php", $https = FALSE, $full = FALSE);
		$params = array();
		$params['id'] = $project['id'];
		$url = url::get($url, $params);
		$item = getNavMenuItem($project['title'], $actionFactory, $innerModules['projectOverview'], $url);
		DOM::append($navMenu, $item);
	}
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