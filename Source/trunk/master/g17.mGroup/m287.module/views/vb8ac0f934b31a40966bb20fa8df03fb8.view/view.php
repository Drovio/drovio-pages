<?php
//#section#[header]
// Module Declaration
$moduleID = 287;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 154;
$innerModules['websiteDesigner'] = 196;
$innerModules['devDashboard'] = 185;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("DEV", "Projects");
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
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "developerDashboardPage", TRUE, TRUE);

// Check if account is TEAM_ADMIN
$teamID = team::getTeamID();
$teamAdmin = accountKey::validateGroup("TEAM_ADMIN", $teamID, accountKey::TEAM_KEY_TYPE);

// Check if there is an active team
if (empty($teamID))
{
	// Get team name
	$teamName = moduleLiteral::get($moduleID, "lbl_noTeam_header", array(), FALSE);
	
	// Select navigation item menu
	$navItem = HTML::select(".listContent.shared_projects")->item(0);
	HTML::addClass($navItem, "selected");
	
	// Add no team popup
	$attr = array();
	$attr['id'] = $projectID;
	$hints = $page->getModuleContainer($innerModules['devDashboard'], $action = "noTeamPopup", $attr, $startup = TRUE, $containerID = "teamChooserContainer");
	$page->append($hints);
}
else
{
	// Get team name
	$teamName = team::getTeamName();
	
	// Select navigation item menu
	$navItem = HTML::select(".listContent.my_projects")->item(0);
	HTML::addClass($navItem, "selected");
}

// Set the team name
$teamField = HTML::select("h2.team")->item(0);
HTML::nodeValue($teamField, $teamName);

// Add edit team button
if (empty($teamID))
{
	$teamInfo = HTML::select(".sideNav.teamInfo")->item(0);
	$title = moduleLiteral::get($moduleID, "lbl_editTeam");
	$editButton = DOM::create("div", $title, "", "edit");
	DOM::append($teamInfo, $editButton);
	
	// Add action
	$actionFactory->setModuleAction($editButton, $innerModules['devDashboard'], $action = "noTeamPopup");
}

// Set section's navigation
$elements = HTML::select(".selectionList .listContent");
foreach ($elements as $element)
	$page->setStaticNav($element, "", "", "", "sectionNav", $display = "none");


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
$q = module::getQuery($moduleID, "get_web_projects");
$attr = array();
$attr['tid'] = $teamID;
$result = $dbc->execute($q, $attr);
$teamProjects = $dbc->fetch($result, TRUE);

// Get shared projects
$sharedProjects = array();
$myProjects = array();
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
	$sharedProjects[$mproject['id']] = $mproject;

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
	
	// Add extra classes for team and shared projects
	if ($project['team_id'] == $teamID)
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
	$projectType = "";
	switch ($project['projectType'])
	{
		case 5:
			HTML::addClass($projectBox, "websites");
			$web_count++;
			$projectType = "Website";
			$designerModule = "websiteDesigner";
			break;
		case 6:
			HTML::addClass($projectBox, "templates");
			$template_count++;
			$projectType = "Web Template";
			$designerModule = "websiteTemplate";
			break;
		case 7:
			HTML::addClass($projectBox, "extensions");
			$extension_count++;
			$projectType = "Web Extension";
			$designerModule = "websiteExtension";
			break;
	}
	
	// Create Box Container
	$projectBoxContainer = DOM::create("div", "", "", "projectBoxContainer");
	HTML::append($projectBox, $projectBoxContainer);
	
	// Box header
	$boxHeader = DOM::create("div", "", "", "boxHeader");
	HTML::append($projectBoxContainer, $boxHeader);
	
	if (isset($myProjects[$project['id']]) || isset($sharedProjects[$project['id']]))
	{
		// Module action attributes
		$attr = array();
		$attr['id'] = $project['id'];
		
		// Set link url
		if (empty($project['name']))
		{
			$url = url::resolve("web", "/websites/website.php");
			$params = array();
			$params['id'] = $project['id'];
			$url = url::get($url, $params);
		}
		else
		{
			$attr['name'] = $project['name'];
			$url = url::resolve("web", "/websites/".$project['name']);
		}
		
		// Create weblink with module action
		$projectLink = $page->getWeblink($url, $boxHeader, "_self", $innerModules[$designerModule], "", $attr);
		HTML::append($projectBoxContainer, $projectLink);
	}
	
	// Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::append($boxHeader, $imageBox);
	// Add icon (if any)
	$prj = new project($project['id']);
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
	
	// Project Title
	$projectTitle = DOM::create("h2", $project['title'], "", "projectTitle");
	HTML::append($boxHeader, $projectTitle);
	
	
	// Project other information
	
	// Team Name
	$teamName = team::getTeamName();
	if ($project['team_id'] != $teamID)
		$teamName = $project['teamName'];
	$teamNameElement = DOM::create("h3", $teamName, "", "pinfo team");
	HTML::append($projectBoxContainer, $teamNameElement);
	
	$ico = DOM::create("span", "", "", "ico");
	DOM::prepend($teamNameElement, $ico);
	
	// Project Type
	$pTypeElement = DOM::create("h3", $projectType, "", "pinfo ptype");
	HTML::append($projectBoxContainer, $pTypeElement);
	
	$ico = DOM::create("span", "", "", "ico");
	DOM::prepend($pTypeElement, $ico);
}

// Add new project icon
// Create project box
$projectBox = DOM::create("li", "", "", "projectBox new");
HTML::prepend($projectList, $projectBox);
HTML::addClass($projectBox, "my");
HTML::addClass($projectBox, "team");
HTML::addClass($projectBox, "shared");

// Create Box Container
$projectBoxContainer = DOM::create("div", "", "", "projectBoxContainer");
HTML::append($projectBox, $projectBoxContainer);

// Box header
$boxHeader = DOM::create("div", "", "", "boxHeader");
HTML::append($projectBoxContainer, $boxHeader);

// Image box
$imageBox = DOM::create("div", "", "", "imageBox");
HTML::append($boxHeader, $imageBox);

// Project Title
$createProjectTitle = moduleLiteral::get($moduleID, "lbl_newProjectTitle");
$projectTitle = DOM::create("h2", $createProjectTitle, "", "projectTitle");
HTML::append($boxHeader, $projectTitle);
$actionFactory->setModuleAction($projectTitle, $moduleID, "projectWizard");

// Project other information

// Team Name
$teamName = team::getTeamName();
$teamNameElement = DOM::create("h3", $teamName, "", "pinfo team");
HTML::append($projectBoxContainer, $teamNameElement);

$ico = DOM::create("span", "", "", "ico");
DOM::prepend($teamNameElement, $ico);

// Project Type
$pTypeElement = DOM::create("h3", "Project Type", "", "pinfo ptype");
HTML::append($projectBoxContainer, $pTypeElement);

$ico = DOM::create("span", "", "", "ico");
DOM::prepend($pTypeElement, $ico);
		


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

// Return output
return $page->getReport();
//#section_end#
?>