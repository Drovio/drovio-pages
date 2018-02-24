<?php
//#section#[header]
// Module Declaration
$moduleID = 373;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPopup'] = 319;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Login");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Login\loginDialog;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "bossLoginPage", TRUE, TRUE);

// Get team information
$teamName = url::getSubDomain();
$dbc = new dbConnection();
$q = $page->getQuery("get_team_info");
$attr = array();
$attr['uname'] = $teamName;
$result = $dbc->execute($q, $attr);
$teamInfo = $dbc->fetch($result);
$teamID = $teamInfo['id'];
$teamInfo = team::info($teamID);
if (!empty($teamInfo['profile_image_url']))
{
	$team_ico = HTML::select(".team_logo .logo")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	DOM::append($team_ico, $img);
}
$team_title = HTML::select(".team_logo .title")->item(0);
HTML::innerHTML($team_title, $teamInfo['name']);


// Get literal for team title
$attr = array();
$attr['tpath'] = $teamName.".".url::getDomain();
$title = moduleLiteral::get($moduleID, "team_login", $attr);
$team_title = HTML::select(".team_title")->item(0);
DOM::append($team_title, $title);

// Set login popup to button
$loginButton = HTML::select(".bossLogin .connect .wbutton.login")->item(0);
$actionFactory->setModuleAction($loginButton, $innerModules['loginPopup'], "", "", $attr = array(), $loading = TRUE);

return $page->getReport();
//#section_end#
?>