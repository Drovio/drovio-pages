<?php
//#section#[header]
// Module Declaration
$moduleID = 378;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "teamPublicProjects", TRUE);


// Get account profile id and username
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');
// Get team information
$dbc = new dbConnection();
$q = $pageContent->getQuery("get_team_info");
$attr = array();
$attr['id'] = $teamID;
$attr['name'] = $teamName;
$result = $dbc->execute($q, $attr);
$teamInfo = $dbc->fetch($result);
$teamID = $teamInfo['id'];


// Get account projects
$dbc = new dbConnection();
$q = $pageContent->getQuery("get_team_projects");
$attr = array();
$attr['id'] = $teamID;
$result = $dbc->execute($q, $attr);
$projects = $dbc->fetch($result, TRUE);
$publicProjects = array();
foreach ($projects as $project)
	if ($project['open'] || $project['public'])
		$publicProjects[] = $project;


// List all public projects
$projectContainer = HTML::select(".publicProjects")->item(0);
if (count($publicProjects) > 0)
	HTML::innerHTML($projectContainer, "");
foreach ($publicProjects as $projectInfo)
{
	// Build a project row
	$prjRow = HTML::create("div", "", "", "prjRow");
	HTML::append($projectContainer, $prjRow);
	
	// Add open project ribbon
	if ($projectInfo['open'])
		HTML::addClass($prjRow, "open");
	
	// Project icon
	$prjIcon = HTML::create("div", "", "", "prjIcon");
	HTML::append($prjRow, $prjIcon);
	
	// Add icon (if any)
	$project = new project($projectInfo['id']);
	$pInfo = $project->info();
	if (isset($pInfo['icon_url']))
	{
		// Add project image
		$img = DOM::create("img");
		DOM::attr($img, "src", $pInfo['icon_url']);
		DOM::append($prjIcon, $img);
	}
	else
		HTML::addClass($prjIcon, "noIcon");
	
	// Project title and weblink
	if (empty($projectInfo['name']))
	{
		$params = array();
		$params['id'] = $projectInfo['id'];
		if ($projectInfo['open'])
			$href = url::resolve("developers", "/open/projects/project.php", $params);
		else
			$href = url::resolve("developers", "/public/project.php", $params);
	}
	else if ($projectInfo['open'])
		$href = url::resolve("developers", "/open/projects/".$projectInfo['name']);
	else
		$href = url::resolve("developers", "/public/".$projectInfo['name']);
	$prjTitle = $pageContent->getWeblink($href, $content = $projectInfo['title'], $target = "_self");
	HTML::addClass($prjTitle, "prjTitle");
	HTML::append($prjRow, $prjTitle);
	
	// Project description
	$prjDesc = HTML::create("div", $projectInfo['description'], "", "prjDesc");
	HTML::append($prjRow, $prjDesc);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>