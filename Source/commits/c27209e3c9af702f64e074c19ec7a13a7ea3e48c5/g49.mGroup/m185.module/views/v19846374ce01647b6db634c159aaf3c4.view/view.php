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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\account;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "dashboardPageContainer", TRUE);

$newProject = HTML::select(".dashboardPageContainer .newProject")->item(0);
$actionFactory->setModuleAction($newProject, $moduleID, "projectWizard");

// Set the team name
$teamName = team::getTeamName();
$teamField = HTML::select("h2.team")->item(0);
HTML::nodeValue($teamField, $teamName);


// Set section's navigation
$elements = HTML::select(".selectionList .listContent");
foreach ($elements as $element)
	NavigatorProtocol::staticNav($element, "", "", "", "sectionNav", $display = "none");


//Get UL
$projectList = HTML::select(".projectList")->item(0);

// Get Team Projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_projects");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$teamProjects = $dbc->fetch($result, TRUE);

// Set sidebar count
$team_count = HTML::select(".selectionList .team_projects .count")->item(0);
DOM::innerHTML($team_count, count($teamProjects));

// Get Account's Projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_account_projects");
$attr = array();
$attr['tid'] = team::getTeamID();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
while ($project = $dbc->fetch($result))
	$myProjects[$project['id']] = $project;
	
// Set sidebar count
$my_count = HTML::select(".selectionList .my_projects .count")->item(0);
DOM::innerHTML($my_count, count($myProjects));

$online_count = 0;
$offline_count = 0;
foreach ($teamProjects as $project)
{
	// Create project box
	$projectBox = DOM::create("li", "", "", "projectBox team");
	HTML::append($projectList, $projectBox);
	
	
	// Add extra classes and count online and offline projects
	if (isset($myProjects[$project['id']]))
		HTML::addClass($projectBox, "my");
	
	if ($project['online'])
	{
		$online_count++;
		HTML::addClass($projectBox, "online");
	}
	else
	{
		$offline_count++;
		HTML::addClass($projectBox, "offline");
	}
	
	
	//Create Container
	$projectBoxContainer = DOM::create("div", "", "", "projectBoxContainer");
	HTML::append($projectBox,$projectBoxContainer);
	
	// Box header
	$boxHeader = DOM::create("div", "", "", "boxHeader");
	HTML::append($projectBoxContainer, $boxHeader);
	
	//Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::append($boxHeader, $imageBox);
	
	//Project Title
	$projectTitle = DOM::create("h3", $project['title'], "", "projectTitle");
	HTML::append($boxHeader, $projectTitle);
	
	// Project description
	$boxDesc = DOM::create("p", $project['description'], "", "projectDesc");
	DOM::append($projectBoxContainer, $boxDesc);
	
	$boxActions = DOM::create("div", "", "", "boxActions");
	DOM::append($projectBoxContainer, $boxActions);
	
	// Project Designer
	$url = url::resolve("developer", "/projects/designer.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$open = moduleLiteral::get($moduleID, "lbl_designerProject");
	$projectLink = $page->getWeblink($url, $open, "_blank");
	$boxTitle = DOM::create("span", $projectLink, "", "ba");
	DOM::append($boxActions, $boxTitle);
	
	// Open Project
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = $project['id'];
	$url = url::get($url, $params);
	$open = moduleLiteral::get($moduleID, "lbl_openProject");
	$projectLink = $page->getWeblink($url, $open, "_blank");
	$boxTitle = DOM::create("span", $projectLink, "", "ba");
	DOM::append($boxActions, $boxTitle);
}

// Set sidebar count
$online_count_span = HTML::select(".selectionList .on_projects .count")->item(0);
DOM::innerHTML($online_count_span, $online_count);

// Set sidebar count
$offline_count_span = HTML::select(".selectionList .off_projects .count")->item(0);
DOM::innerHTML($offline_count_span, $offline_count);

if (count($teamProjects) == 0)
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