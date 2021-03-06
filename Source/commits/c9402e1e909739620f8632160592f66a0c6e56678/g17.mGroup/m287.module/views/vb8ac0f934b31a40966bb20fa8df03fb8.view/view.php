<?php
//#section#[header]
// Module Declaration
$moduleID = 287;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 154;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Profile\team;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Websites\website;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "webDashboardPage", TRUE);


// Check if there is an active team
$teamID = team::getTeamID();
if (empty($teamID))
{
	// Remove all containers and keep team chooser
	$sidebar = HTML::select(".projectContainer .toolbar")->item(0);
	HTML::replace($sidebar, NULL);
	
	$contentMain = HTML::select(".projectContainer .boxes")->item(0);
	HTML::replace($contentMain, NULL);
	
	$teamContainer = HTML::select(".projectContainer .teams")->item(0);
	
	// Get all account teams
	$accountTeams = team::getAccountTeams();
	foreach ($accountTeams as $ateam)
	{
		$teamName = DOM::create("h3", $ateam['name'], "", "tn");
		DOM::append($teamContainer, $teamName);
		
		$attr = array();
		$attr['teamID'] = $ateam['id'];
		$attr['tid'] = $ateam['id'];
		$actionFactory->setModuleAction($teamName, $innerModules['accountInfo'], "switchTeam", "", $attr);
	}
	
	if (!empty($accountTeams))
	{
		$noteam = HTML::select(".htitle.noteam")->item(0);
		HTML::replace($noteam, NULL);
	}
}
else
{
	// Remove the team chooser dialog
	$teamChooser = HTML::select(".projectContainer .teamChooser")->item(0);
	HTML::replace($teamChooser, NULL);
}

// Set the team name
$teamName = team::getTeamName();
$teamField = HTML::select("h2.team")->item(0);
HTML::nodeValue($teamField, $teamName);

// Get project container
$projectContainer = HTML::select(".webDashboardPage .projectContainer")->item(0);


// Get all web projects
// Get Team Projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_web_projects");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
while ($project = $dbc->fetch($result))
	switch ($project['projectType'])
	{
		case 5:
			$wsProjects[] = $project;
			break;
		case 6:
			$tmplProjects[] = $project;
			break;
		case 7:
			$extProjects[] = $project;
			break;
	}


// Websites
$wsContainer = HTML::select(".webDashboardPage .projectContainer .boxContainer.ws .body")->item(0);
$openUrl = url::resolve("web", "/websites/website.php");
addProjects($wsContainer, $wsProjects, $openUrl, $moduleID);

// Extensions
$wsContainer = HTML::select(".webDashboardPage .projectContainer .boxContainer.cmp .body")->item(0);
$openUrl = url::resolve("developer", "/projects/index.php");
addProjects($wsContainer, $extProjects, $openUrl, $moduleID);

// Templates
addProjects($wsContainer, $tmplProjects, $openUrl, $moduleID);

// Return output
return $page->getReport();



// Add projects to the given container
function addProjects($container, $projects, $openUrl, $moduleID)
{
	foreach ($projects as $project)
	{
		$row = DOM::create("div", "", "", "prow");
		DOM::append($container, $row);
		
		$ico = DOM::create("div", "", "", "ico");
		DOM::append($row, $ico);
		
		$type = DOM::create("div", $project['projectTypeName'], "", "type");
		DOM::append($row, $type);
		
		$pTitle = DOM::create("div", $project['title'], "", "title");
		DOM::append($row, $pTitle);
		
		$title = moduleLiteral::get($moduleID, "lbl_openProject");
		$openLink = DOM::create("a", $title, "", "openl");
		DOM::append($row, $openLink);
		
		// Set url attributes
		DOM::attr($openLink, "target", "_blank");
		
		// Set url
		$params = array();
		$params['id'] = $project['id'];
		$url = url::get($openUrl, $params);
		DOM::attr($openLink, "href", $url);
	}
}
//#section_end#
?>