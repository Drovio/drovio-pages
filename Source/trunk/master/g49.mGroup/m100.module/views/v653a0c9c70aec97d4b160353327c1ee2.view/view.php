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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Profile\account;
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "tlb-developerNavContainer", TRUE);

// Build the menu
$navMenu = HTML::select(".navMenu")->item(0);

// List projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$myProjects = $dbc->fetch($result, TRUE);

// Get projects organized by team
$teams = array();
foreach ($myProjects as $project)
	$teams[$project['team_id']] = team::info($project['team_id']);
uasort($teams, "sort_teams");
// Get teams
$teamList = HTML::select(".tlb-developerNav .teamList")->item(0);
$projectListContainer = HTML::select(".tlb-developerNav .projectList")->item(0);
foreach ($teams as $teamID => $teamInfo)
{
	// Add navigation item
	$teamItem = DOM::create("li", "", "", "titem tid".$teamID);
	DOM::attr($teamItem, "data-tid", $teamID);
	DOM::append($teamList, $teamItem);
	if ($teamID == team::getTeamID())
		HTML::addClass($teamItem, "selected");
	
	// Team image
	$img = NULL;
	if (isset($teamInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $teamInfo['profile_image_url']);
	}
	$ico = DOM::create("div", $img, "", "ico");
	DOM::append($teamItem, $ico);
	
	// Team title
	$title = DOM::create("div", $teamInfo['name'], "", "title");
	DOM::append($teamItem, $title);
	
	// Create team list container
	$teamContainer = DOM::create("div", "", "", "tcontainer tid_".$teamID);
	DOM::append($projectListContainer, $teamContainer);
}


// Add projects to containers	
foreach ($myProjects as $project)
{
	$teamID = $project['team_id'];
	$projectContainer = HTML::select(".tcontainer.tid_".$teamID)->item(0);
	
	// Set team projects
	if (empty($project['name']))
	{
		$params = array();
		$params['id'] = $project['id'];
		$url = url::resolve("developers", $url = "/dashboard/project.php", $params);
	}
	else
		$url = url::resolve("developers", $url = "/dashboard/".$project['name']);
	$item = getProjectTile($project['id'], $project['name'], $project['title'], $actionFactory, $innerModules['projectOverview'], $url, $pageContent);
	DOM::append($projectContainer, $item);
}


// My Dashboard Home
$item = HTML::select(".tlb-developerNav .shrct.dashboard")->item(0);
$actionFactory->setModuleAction($item, $innerModules['dashboard']);

// Create new project
$item = HTML::select(".tlb-developerNav .shrct.new_project")->item(0);
$actionFactory->setModuleAction($item, $innerModules['dashboard'], "projectWizard");

// Return output
return $pageContent->getReport();

// Sort teams by name
function sort_teams($teamA, $teamB)
{
	return (strtolower($teamA['name']) < strtolower($teamB['name']) ? -1 : 1);
}

// Create toolbar nav item
function getProjectTile($projectID, $projectName, $header, $actionFactory, $moduleID, $href, $pageContent)
{
	// Create item
	$item = DOM::create("div", "", "", "pitem");
	
	// Create weblink
	$itemA = $pageContent->getWeblink($href, $header, "_self");
	DOM::append($item, $itemA);
	
	// Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::prepend($itemA, $imageBox);
	
	// Add icon (if any)
	$prj = new project($projectID);
	$projectIconUrl = $prj->getIconUrl();
	if (!empty($projectIconUrl))
	{
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIconUrl);
		DOM::append($imageBox, $img);
	}
	else
		HTML::addClass($imageBox, "noIcon");
	
	// Set module action for project
	if (!empty($moduleID))
	{
		$attr = array();
		$attr['id'] = $projectID;
		$attr['name'] = $projectName;
		$actionFactory->setModuleAction($itemA, $moduleID, $viewName = "", $holder = "", $attr);
	}
	
	return $item;
}
//#section_end#
?>