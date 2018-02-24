<?php
//#section#[header]
// Module Declaration
$moduleID = 259;

// Inner Module Codes
$innerModules = array();
$innerModules['teamPage'] = 351;
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
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "myTeamsContainer", TRUE);

// List all teams
$currentTeamContainer = HTML::select(".myTeams .currentTeam")->item(0);
$allTeamContainer = HTML::select(".myTeams .allTeams")->item(0);

// List all teams
$currentTeamID = team::getTeamID();
$teams = team::getAccountTeams();
foreach ($teams as $team)
{
	// Initialize
	$teamID = $team['id'];
	$teamName = $team['name'];
	
	// Select container for team
	$teamContainer = ($team['id'] == $currentTeamID ? $currentTeamContainer : $allTeamContainer);
	
	// Create project box
	$teamBox = DOM::create("li", "", "", "teamBox");
	DOM::append($teamContainer, $teamBox);
	
	// Create Box Container
	$teamBoxContainer = DOM::create("div", "", "", "teamBoxContainer");
	HTML::append($teamBox, $teamBoxContainer);
	
	// Box header
	$boxHeader = DOM::create("div", "", "", "boxHeader");
	HTML::append($teamBoxContainer, $boxHeader);
	
	// Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::append($boxHeader, $imageBox);
	
	// Add profile image (if any)
	if (isset($team['profile_image_url']))
	{
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $team['profile_image_url']);
		DOM::append($imageBox, $img);
	}
	
	// Team Name
	$teamNameHeader = DOM::create("h2", $teamName, "", "teamName");
	HTML::append($boxHeader, $teamNameHeader);
	
	
	// Team roles (according to keys)
	$keys = accountKey::get();
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 && $key['context'] == $teamID)
			$roles[] = $key['groupName'];
	$roleContext = implode(", ", $roles);
	$rolesElement = DOM::create("h3", $roleContext, "", "tinfo roles");
	HTML::append($teamBoxContainer, $rolesElement);
	// Roles ico
	$ico = DOM::create("span", "", "", "ico");
	DOM::prepend($rolesElement, $ico);
	
	
	// Add team actions
	$boxActions = DOM::create("div", "", "", "boxActions");
	DOM::append($teamBoxContainer, $boxActions);
	
	// Show team relation info
	$uTeamName = $team['uname'];
	if (!empty($uTeamName))
		$url = url::resolve("my", "/relations/".$uTeamName);
	else
	{
		$params = array();
		$params['id'] = $teamID;
		$url = url::resolve("my", "/relations/team.php");
		$url = url::get($url, $params);
	}
	$baTitle = moduleLiteral::get($moduleID, "lbl_teamDetails");
	$boxActionItem = $pageContent->getWeblink($url, $baTitle, "_self");
	HTML::addClass($boxActionItem, "ba action");
	DOM::append($boxActions, $boxActionItem);
	
	$attr = array();
	$attr['id'] = $teamID;
	$attr['name'] = $uTeamName;
	$actionFactory->setModuleAction($boxActionItem, $innerModules['teamPage'], "", "", $attr);
	
	if ($team['id'] != $currentTeamID)
	{
		// Set team active (if not)
		$title = moduleLiteral::get($moduleID, "lbl_switchToTeam");
		$boxActionItem = DOM::create("div", $title, "", "ba action");
		DOM::append($boxActions, $boxActionItem);
		
		// Set action
		$attr = array();
		$attr['tid'] = $teamID;
		$actionFactory->setModuleAction($boxActionItem, $innerModules['accountInfo'], "switchTeam", "", $attr);
	}
}

// Add 'Create New Team' box
if (account::isAdmin())
{
	// Create project box
	$teamBox = DOM::create("li", "", "", "teamBox new");
	DOM::append($currentTeamContainer, $teamBox);
	
	// Create Box Container
	$teamBoxContainer = DOM::create("div", "", "", "teamBoxContainer");
	HTML::append($teamBox, $teamBoxContainer);
	
	// Box header
	$boxHeader = DOM::create("div", "", "", "boxHeader");
	HTML::append($teamBoxContainer, $boxHeader);
	
	// Image box
	$imageBox = DOM::create("div", "", "", "imageBox");
	HTML::append($boxHeader, $imageBox);
	
	// Team Name
	$createTeamTitle = moduleLiteral::get($moduleID, "lbl_newTeamName");
	$teamNameHeader = DOM::create("h2", $createTeamTitle, "", "teamName");
	HTML::append($boxHeader, $teamNameHeader);
	
	// Team roles (according to keys)
	$rolesElement = DOM::create("h3", "TEAM_ROLE", "", "tinfo roles");
	HTML::append($teamBoxContainer, $rolesElement);
	// Roles ico
	$ico = DOM::create("span", "", "", "ico");
	DOM::prepend($rolesElement, $ico);
	
	
	// Add team actions
	$boxActions = DOM::create("div", "", "", "boxActions");
	DOM::append($teamBoxContainer, $boxActions);
	
	// Create new team dialog
	$title = moduleLiteral::get($moduleID, "lbl_createTeam");
	$boxActionItem = DOM::create("div", $title, "", "ba action");
	DOM::append($boxActions, $boxActionItem);
	
	$attr = array();
	$attr['name'] = $teamName;
	$actionFactory->setModuleAction($boxActionItem, $moduleID, "createTeam", "", $attr);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>