<?php
//#section#[header]
// Module Declaration
$moduleID = 349;

// Inner Module Codes
$innerModules = array();
$innerModules['bossHome'] = 88;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\team;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get team profile id and name
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');
if (empty($teamID) && empty($teamName))
{
	// Redirect to proper url
	$teamName = team::getTeamUname();
	$teamID = team::getTeamID();
	
	// Check user team
	if (empty($teamID) && empty($teamName))
		$url = url::resolve("boss", "/");
	else if (!empty($teamName))
		$url = url::resolve("boss", "/profile/".$teamName);
	else
	{
		$url = url::resolve("boss", "/profile/index.php");
		$params = array();
		$params['id'] = $teamID;
		$url = url::get($url, $params);
	}
	
	// Return redirect report
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}


// Get account information
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_info");
$attr = array();
$attr['id'] = $teamID;
$attr['name'] = $teamName;
$result = $dbc->execute($q, $attr);
$teamInfo = $dbc->fetch($result);
$teamID = $teamInfo['id'];

// Build the page content
$page->build($teamInfo['name'], "bossTeamProfilePage", TRUE);

// Set team title
$teamTitle = HTML::select(".bossTeamProfile .header .title")->item(0);
HTML::innerHTML($teamTitle, $teamInfo['name']);

/*
// Get account projects
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['id'] = $accountID;
$result = $dbc->execute($q, $attr);
$projects = $dbc->fetch($result, TRUE);
$publicProjects = array();
foreach ($projects as $project)
	if ($project['public'])
		$publicProjects[] = $project;

$attr = array();
$attr['count'] = count($projects);
$title = moduleLiteral::get($moduleID, "lbl_project_count", $attr);
$pcountTitle = HTML::select(".devProfile .header .pcount")->item(0);
HTML::append($pcountTitle, $title);

$attr = array();
$attr['count'] = count($publicProjects);
$title = moduleLiteral::get($moduleID, "lbl_publicProjects", $attr);
$pcountTitle = HTML::select(".devProfile .portfolio .projects .hd")->item(0);
HTML::append($pcountTitle, $title);


// List all public projects
$projectContainer = HTML::select(".devProfile .portfolio .projects")->item(0);
foreach ($publicProjects as $projectInfo)
{
	// Build a project row
	$prjRow = HTML::create("div", "", "", "prjRow");
	HTML::append($projectContainer, $prjRow);
	
	// Project icon
	$prjIcon = HTML::create("div", "", "", "prjIcon");
	HTML::append($prjRow, $prjIcon);
	
	// Add icon (if any)
	$project = new project($projectInfo['id']);
	$projectIcon = $project->getResourcesFolder()."/.assets/icon.png";
	if (file_exists(systemRoot.$projectIcon))
	{
		// Resolve path
		$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
		$projectIcon = url::resolve("repo", $projectIcon);
		
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIcon);
		DOM::append($prjIcon, $img);
	}
	else
		HTML::addClass($prjIcon, "noIcon");
	
	// Project title
	$href = url::resolve("open", "/projects/project.php");
	$params = array();
	$params['id'] = $projectInfo['id'];
	$href = url::get($href, $params);
	$prjTitle = $page->getWeblink($href, $content = $projectInfo['title'], $target = "_blank");
	HTML::addClass($prjTitle, "prjTitle");
	HTML::append($prjRow, $prjTitle);
	
	// Project description
	$prjDesc = HTML::create("div", $projectInfo['description'], "", "prjDesc");
	HTML::append($prjRow, $prjDesc);
}
*/

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['bossHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$featuresPage = HTML::select(".bossTeamProfile")->item(0);
$footerMenu = module::loadView($innerModules['bossHome'], "footerMenu");
DOM::append($featuresPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>