<?php
//#section#[header]
// Module Declaration
$moduleID = 169;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("AEL", "Platform");
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("DEV", "Profile");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Apps");
importer::import("UI", "Content");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \ESS\Environment\session;
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\loaders\AppLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \API\Model\modules\module;
use \API\Model\apps\application;
use \API\Model\apps\appSessionManager;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \UI\Content\HTMLFrame;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application as DEVApp;
use \DEV\Profile\team as devTeam;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get application id
$appID = engine::getVar('id');
$appName = engine::getVar('name');
$app = new DEVApp($appID, $appName);
$appID = $app->getID();

// Get application info
$appLastVersion = appSessionManager::getInstance()->getLastVersion($appID);
$appVersion = appSessionManager::getInstance()->getVersion($appID);
if (!empty($appLastVersion) && $appVersion != $appLastVersion)
{
	appSessionManager::getInstance()->setVersion($appID, $appLastVersion);
	$appVersion = $appLastVersion;
}
$appInfo = application::getApplicationInfo($appID, $appVersion);

// Show ad for team's free plan
$teamPlanID = devTeam::getCurrentPlan($live = FALSE, $appInfo['team_id']);
$showAd = ($teamPlanID < 2);

// Build the module for a valid application
$page->build($appInfo['title'], "applicationPlayer".($showAd ? " with_ad": ""), TRUE);
$applicationContainer = HTML::select(".applicationPlayer #applicationContainer")->item(0);

// If there is no version active, return empty page
if (empty($appVersion))
{
	// Return output
	return $page->getReport();
}

// Create one-time token and save to session
$token = "tk".md5("app_center_frame_player_token_".$appID."_".$appName."_".time()."_".mt_rand());
session::set("app_center_frame_player_token", $token, $namespace = "application_center");

// Get frame player url
if (empty($appName))
{
	$params = array();
	$params['id'] = $appID;
	$params['token'] = $token;
	$src = url::resolve("apps", "/fplayer.php", $params);
}
else
	$src = url::resolve("apps", "/".$appName."/fplay/".$token);

// Create iframe
$iframe = new HTMLFrame();
$appPlayerFrame = $iframe->build($src, $name = "applicationFPlayer", $id = "applicationFPlayer", $class = "", $sandbox = array())->get();
DOM::append($applicationContainer, $appPlayerFrame);

// Add action to show back button in navigation bar
$page->addReportAction("appcenter.navigation.showhide_back", 1);

// Close sidebar
$page->addReportAction("appcenter.sidebar.close");

// Add app info href
if (!empty($appName))
	$appInfoUrl = url::resolve("apps", "/".$appName);
else
{
	$params = array();
	$params['id'] = $appID;
	$appInfoUrl = url::resolve("apps", "/application.php", $params);
}
$page->addReportAction("appcenter.navigation.appinfo_href", $appInfoUrl);

// Handle ad container
$adContainer = HTML::select(".adContainer")->item(0);
if (!$showAd)
	HTML::replace($adContainer, NULL);

// Return output
return $page->getReport();
//#section_end#
?>