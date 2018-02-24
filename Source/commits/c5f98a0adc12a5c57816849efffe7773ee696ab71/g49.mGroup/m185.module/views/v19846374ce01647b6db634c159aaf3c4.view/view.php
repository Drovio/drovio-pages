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
importer::import("DEV", "Resources");
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
use \DEV\Resources\paths;

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


// Get UL
$projectList = HTML::select(".projectList")->item(0);

// Get My Projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_my_projects");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$allMyProjects = $dbc->fetch($result, TRUE);

// Get Team Projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_projects");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$teamProjects = $dbc->fetch($result, TRUE);

// Get shared projects
$sharedProjects = array();
foreach ($teamProjects as $tproject)
	foreach ($allMyProjects as $id => $mproject)
	{
		if ($tproject['team_id'] != $mproject['team_id'])
			$sharedProjects[$mproject['id']] = $mproject;
		else
			$myProjects[$mproject['id']] = $mproject;
		unset($allMyProjects[$id]);
	}
	
foreach ($allMyProjects as $mproject)
	$sharedProjects[] = $mproject;

// Set sidebar count
$team_count = HTML::select(".selectionList .team_projects .count")->item(0);
DOM::innerHTML($team_count, "".count($teamProjects));

// Set sidebar count
$shared_count = HTML::select(".selectionList .shared_projects .count")->item(0);
DOM::innerHTML($shared_count, "".count($sharedProjects));

// Set sidebar count
$my_count = HTML::select(".selectionList .my_projects .count")->item(0);
DOM::innerHTML($my_count, "".count($myProjects));

$online_count = 0;
$offline_count = 0;
$allProjects = array_merge($teamProjects, $sharedProjects);
foreach ($allProjects as $project)
{
	// Create project box
	$projectBox = DOM::create("li", "", "", "projectBox");
	HTML::append($projectList, $projectBox);
	
	// Add extra classes and count online and offline projects and types
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
	
	// Add extra classes for team and shared projects
	if ($project['team_id'] == team::getTeamID())
	{
		$team_count++;
		HTML::addClass($projectBox, "team");
	}
	else
	{
		$shared_count++;
		HTML::addClass($projectBox, "shared");
	}
	
	// Set counter
	switch ($project['projectType'])
	{
		case 1:
		case 2:
		case 3:
			HTML::addClass($projectBox, "redback");
			$red_count++;
			break;
		case 4:
			HTML::addClass($projectBox, "apps");
			$app_count++;
			break;
		case 5:
			HTML::addClass($projectBox, "websites");
			$web_count++;
			break;
		case 6:
			HTML::addClass($projectBox, "templates");
			$template_count++;
			break;
		case 7:
			HTML::addClass($projectBox, "extensions");
			$extension_count++;
			break;
	}
	
	
	// Create Box Container
	$projectBoxContainer = DOM::create("div", "", "", "projectBoxContainer");
	HTML::append($projectBox,$projectBoxContainer);
	
	// Box header
	$boxHeader = DOM::create("div", "", "", "boxHeader");
	HTML::append($projectBoxContainer, $boxHeader);
	
	// Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::append($boxHeader, $imageBox);
	// Add icon (if any)
	$prj = new project($project['id']);
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
	
	//Project Title
	$projectTitle = DOM::create("h3", $project['title'], "", "projectTitle");
	HTML::append($boxHeader, $projectTitle);
	
	// Project description
	$boxDesc = DOM::create("p", $project['description'], "", "projectDesc");
	DOM::append($projectBoxContainer, $boxDesc);
	
	
	// Check if it's an account project
	if (isset($myProjects[$project['id']]) || isset($sharedProjects[$project['id']]))
	{
		// Add my class
		if (isset($myProjects[$project['id']]))
			HTML::addClass($projectBox, "my");
	
		// Add project actions
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
}

// Set sidebar count
$online_count_span = HTML::select(".selectionList .on_projects .count")->item(0);
DOM::innerHTML($online_count_span, "".$online_count);

// Set sidebar count
$offline_count_span = HTML::select(".selectionList .off_projects .count")->item(0);
DOM::innerHTML($offline_count_span, "".$offline_count);

// If there are no red projects, remove the item from the side menu
if ($red_count == 0 || empty($red_count))
{
	$redSection = HTML::select(".selectionList .redProjects")->item(0);
	DOM::replace($redSection, NULL);
}
else
{
	$red_count_span = HTML::select(".selectionList .redProjects .count")->item(0);
	DOM::innerHTML($red_count_span, $red_count);
}

// Applications Count
if ($app_count == 0 || empty($app_count))
{
	$category = HTML::select(".selectionList .apps")->item(0);
	HTML::replace($category, NULL);
}
else
{
	$app_count_span = HTML::select(".selectionList .apps .count")->item(0);
	DOM::innerHTML($app_count_span, $app_count);
}

// Websites Count
if ($web_count == 0 || empty($web_count))
{
	$category = HTML::select(".selectionList .webProjects")->item(0);
	HTML::replace($category, NULL);
}
else
{
	$web_count_span = HTML::select(".selectionList .webProjects .count")->item(0);
	DOM::innerHTML($web_count_span, $web_count);
}

// Web Templates Count
if ($template_count == 0 || empty($template_count))
{
	$category = HTML::select(".selectionList .webTemplates")->item(0);
	HTML::replace($category, NULL);
}
else
{
	$template_count_span = HTML::select(".selectionList .webTemplates .count")->item(0);
	DOM::innerHTML($template_count_span, $template_count);
}

// Web Templates Count
if ($extension_count == 0 || empty($extension_count))
{
	$category = HTML::select(".selectionList .webExtensions")->item(0);
	HTML::replace($category, NULL);
}
else
{
	$extension_count_span = HTML::select(".selectionList .webExtensions .count")->item(0);
	DOM::innerHTML($extension_count_span, $extension_count);
}

if (count($sharedProjects) == 0 && count($myProjects) == 0)
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