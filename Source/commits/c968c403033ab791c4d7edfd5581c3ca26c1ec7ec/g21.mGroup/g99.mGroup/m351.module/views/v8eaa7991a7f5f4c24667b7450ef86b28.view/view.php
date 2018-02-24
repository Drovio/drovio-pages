<?php
//#section#[header]
// Module Declaration
$moduleID = 351;

// Inner Module Codes
$innerModules = array();
$innerModules['info'] = 353;
$innerModules['relations'] = 354;
$innerModules['members'] = 260;

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
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get team information
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');
/*
// Get team information
if (empty($teamID))
{
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "get_team_info");
	$attr = array();
	$attr['tname'] = $teamName;
	$result = $dbc->execute($q, $attr);
	$info = $dbc->fetch($result);
	$teamID = $info['id'];
}*/

// Validate team (only for team members for now)
$validTeam = FALSE;
$teams = team::getAccountTeams();
foreach ($teams as $team)
	if ($team['id'] == $teamID || (!empty($team['uname']) && $team['uname'] == $teamName))
	{
		$teamID = $team['id'];
		$validTeam = TRUE;
		break;
	}
	
// If not valid, show page with error
if (!$validTeam)
{
	// Build module page
	$title = moduleLiteral::get($moduleID, "lbl_title", array(), FALSE);
	$page->build($title, "teamProfilePage");
	
	// Create team error
	
	// Return report
	return $page->getReport();
}

// Get team public information
$teamInfo = team::info($teamID);
$teamName = $teamInfo['name'];

// Build the module content
$title = moduleLiteral::get($moduleID, "lbl_title", array(), FALSE);
$page->build($teamName." | ".$title, "teamProfilePage", TRUE);

// Set team name
$teamNameContainer = HTML::select(".teamName")->item(0);
HTML::innerHTML($teamNameContainer, $teamName);

// Set team profile picture
if (isset($teamInfo['profile_image_url']))
{
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	
	// Append to logo
	$logo = HTML::select(".teamProfile .logoBox .logo")->item(0);
	DOM::append($logo, $img);
}

// Add leave module action
$leaveItem = HTML::select(".teamInfoContainer .logoBox .leaveTeam")->item(0);
$attr = array();
$attr['tid'] = $teamID;
$actionFactory->setModuleAction($leaveItem, $moduleID, "leaveTeam", "", $attr);

// Set navigation
$items = array();
$items['info'] = "teamInfo";
$items['relations'] = "teamRelations";
$items['members'] = "teamMembers";

// Check if user is team admin
if (!accountKey::validateGroup("TEAM_ADMIN", $teamID, accountKey::TEAM_KEY_TYPE))
{
	// Remove navigation item
	unset($items['privileges']);
	$adminItem = HTML::select(".teamNavigation .navitem.privileges")->item(0);
	HTML::replace($adminItem);
}

foreach ($items as $class => $ref)
{
	// Set nav item
	$item = HTML::select(".teamNavigation .navitem.".$class)->item(0);
	NavigatorProtocol::staticNav($item, $ref, "teamDetailsContainer", "teamGroup", "teamNavGroup", $display = "none");
	
	// Avoid empty modules
	if (empty($innerModules[$class]))
		continue;
	
	// Get module container
	$teamDetailsContainer = HTML::select(".teamDetailsContainer")->item(0);
	$attr = array();
	$attr['id'] = $teamID;
	$attr['name'] = $teamName;
	$mContainer = $page->getModuleContainer($innerModules[$class], $viewName = "", $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload = TRUE);
	NavigatorProtocol::selector($mContainer, "teamGroup");
	HTML::append($teamDetailsContainer, $mContainer);
}

// Return output
return $page->getReport();
//#section_end#
?>