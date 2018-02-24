<?php
//#section#[header]
// Module Declaration
$moduleID = 366;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\session;
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "drovioSubDialog", TRUE);

// Get account teams
$teamContainer = HTML::select(".drovioSub .teams")->item(0);
$teams = team::getAccountTeams();
if (!empty($teams))
	HTML::innerHTML($teamContainer, "");
foreach ($teams as $index => $teamInfo)
{
	// Check team uname
	if (empty($teamInfo['uname']))
	{
		unset($teams[$index]);
		continue;
	}
		
	// Create weblink
	$href = url::resolve($teamInfo['uname'], "/");
	$ttile = $pageContent->getWeblink($href, $content = "", $target = "_self", $mID = "", $viewName = "", $attr = array(), $class = "ttile");
	DOM::append($teamContainer, $ttile);
	
	// Create team icon
	$img = NULL;
	if (!empty($teamInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $teamInfo['profile_image_url']);
	}
	$icon = DOM::create("div", $img, "", "icon");
	DOM::append($ttile, $icon);
	
	$title = DOM::create("div", $teamInfo['name'], "", "title");
	DOM::append($ttile, $title);
}

// Create new team action
$newTeam = HTML::select(".navitem.new")->item(0);
$actionFactory->setModuleAction($newTeam, $moduleID, "createTeam");



// Check if there is a pending team creation
$teamName = session::get("create_new_team", NULL, "team_creator");
if (!empty($teamName))
{
	// Remove team from session
	session::remove("create_new_team", "team_creator");
	
	// Create a new team and add this account to it
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "create_team");
	
	// Set attributes and execute
	$attr = array();
	$attr['uname'] = $teamName;
	$attr['name'] = $teamName;
	$attr['aid'] = account::getAccountID();
	$result = $dbc->execute($dbq, $attr);
	
	// If there is an error in creating team, show it
	if ($result)
	{
		// Add account to TEAM_ADMIN and add key for the same group
		$teamInfo = $dbc->fetch($result);
		$teamID = $teamInfo['id'];
		privileges::addAccountToGroup(account::getAccountID(), "TEAM_ADMIN");
		accountKey::create(6, accountKey::TEAM_KEY_TYPE, $teamID);
		
		// Redirect to team dashboard
		return $actionFactory->getReportRedirect("/", $teamName, $formSubmit = TRUE);
	}
}


// Return output
return $pageContent->getReport();
//#section_end#
?>