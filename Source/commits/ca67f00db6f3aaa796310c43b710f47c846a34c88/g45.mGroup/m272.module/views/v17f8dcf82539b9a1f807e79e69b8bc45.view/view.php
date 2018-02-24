<?php
//#section#[header]
// Module Declaration
$moduleID = 272;

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
importer::import("AEL", "Platform");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("BSS", "Market");
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("UI", "Apps");
importer::import("UI", "Content");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \ESS\Environment\session;

use \ESS\Protocol\BootLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \AEL\Platform\application as appPlayer;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\team;
use \UI\Modules\MContent;
use \UI\Apps\APPContent;
use \UI\Content\HTMLFrame;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;
use \BSS\Market\appMarket;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get team id
$teamID = team::getTeamID();
$teamInfo = team::info($teamID);
$teamName = $teamInfo['uname'];

// Get application id
$appID = engine::getVar('appID');
$app = new application($appID);
$appID = $app->getID();
$appInfo = $app->info();
$appName = $appInfo['name'];

// Build application player
$pageContent->build("", "applicationPlayer", TRUE);
$appPlayerWrapper = HTML::select(".enp-appPlayerWrapper")->item(0);

// Update application version (if there is a next version to update)
appMarket::updateTeamAppVersion($appID);
$teamAppVersion = appMarket::getTeamAppVersion($appID);

// Set toolbar elements
$applicationInfo = appMarket::getApplicationInfo($appID, $teamAppVersion);
if (!empty($applicationInfo['icon_url']))
{
	$appIco = HTML::select(".enp-navbar .app-ico")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $applicationInfo['icon_url']);
	DOM::append($appIco, $img);
}
$appTitle = HTML::select(".enp-navbar .app-title")->item(0);
DOM::innerHTML($appTitle, $applicationInfo['title']);



// Create one-time token and save to session
$token = "tk".md5("boss_frame_player_token".$appID."_".$appName."_".time()."_".mt_rand());
session::set("boss_frame_player_token", $token, $namespace = "enterprise_dashboard");

// Get frame player url
if (empty($appName))
{
	$params = array();
	$params['id'] = $appID;
	$params['token'] = $token;
	$src = url::resolve($teamName, "/efplayer.php", $params);
}
else
	$src = url::resolve($teamName, "/apps/".$appName."/efplay/".$token);

// Create iframe
$iframe = new HTMLFrame();
$appPlayerFrame = $iframe->build($src, $name = "applicationFPlayer", $id = "applicationFPlayer", $class = "", $sandbox = array())->get();
DOM::append($appPlayerWrapper, $appPlayerFrame);

// Set data application
$pageContainer = $pageContent->get();
$appData = array();
$appData['id'] = $appID;
HTML::data($pageContainer, "app", $appData);

// Add switch application action
$pageContent->addReportAction("application.switch", $appID);

// Return output
return $pageContent->getReport(".apps_pool", "append");
//#section_end#
?>