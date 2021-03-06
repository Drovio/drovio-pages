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
	$teamInfo = team::info();
	$teamName = $teamInfo['uname'];
	$teamID = team::getTeamID();
	
	// Check user team
	if (empty($teamInfo))
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


// Get team id from name
if (empty($teamID))
{
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "get_team_info");
	$attr = array();
	$attr['id'] = $teamID;
	$attr['name'] = $teamName;
	$result = $dbc->execute($q, $attr);
	$teamInfo = $dbc->fetch($result);
	$teamID = $teamInfo['id'];
}

// Get team information
$teamInfo = team::info($teamID);

// Build the page content
$page->build($teamInfo['name'], "bossTeamProfilePage", TRUE);


// Add team information

// Set team name
$teamNameContainer = HTML::select(".teamInfoContainer .teamName")->item(0);
HTML::innerHTML($teamNameContainer, $teamInfo['name']);

// Set team profile picture
if (isset($teamInfo['profile_image_url']))
{
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	
	// Append to logo
	$logo = HTML::select(".bossTeamProfile .logoBox .logo")->item(0);
	DOM::append($logo, $img);
}



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