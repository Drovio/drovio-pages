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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\account;
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "developerNav", TRUE);

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
	$teams[$project['team_id']] = $project['teamName'];
	
// Get teams
$teamList = HTML::select(".developerNav .projectList .teamList")->item(0);
$projectListContainer = HTML::select(".developerNav .projectList .pList")->item(0);
foreach ($teams as $teamID => $teamName)
{
	// Add navigation item
	$teamItem = DOM::create("li", $teamName, "", "titem tid".$teamID);
	DOM::attr($teamItem, "data-tid", $teamID);
	DOM::append($teamList, $teamItem);
	if ($teamID == team::getTeamID())
		HTML::addClass($teamItem, "selected");
	
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
	$url = url::resolve("developer", $url = "/projects/project.php", $https = FALSE, $full = FALSE);
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$item = getProjectTile($project['id'], $project['title'], $actionFactory, $innerModules['projectOverview'], $url, $pageContent);
	DOM::append($projectContainer, $item);
}


// My Dashboard Home
$item = HTML::select(".devDashboard a")->item(0);
$actionFactory->setModuleAction($item, $innerModules['dashboard']);

// Return output
return $pageContent->getReport();


// Create toolbar nav item
function getProjectTile($projectID, $header, $actionFactory, $moduleID, $href, $pageContent)
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
	$projectIcon = $prj->getResourcesFolder()."/.assets/icon.png";
	if (file_exists(systemRoot.$projectIcon))
	{
		// Resolve path
		$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
		$projectIcon = url::resolve("repo", $projectIcon);
		
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIcon);
		DOM::append($imageBox, $img);
	}
	else
		HTML::addClass($imageBox, "noIcon");
	
	
	if (!is_null($moduleID))
		$actionFactory->setModuleAction($itemA, $moduleID);
	
	return $item;
}
//#section_end#
?>