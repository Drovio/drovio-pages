<?php
//#section#[header]
// Module Declaration
$moduleID = 384;

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
importer::import("BSS", "Market");
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Apps");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\session;
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \AEL\Platform\application as appPlayer;
use \API\Model\apps\application;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application as DEVApplication;
use \BSS\Market\appMarket;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get token and validate
$token = engine::getVar("token");
$sessionToken = session::get("boss_frame_player_token", NULL, $namespace = "enterprise_dashboard");
if ($token != $sessionToken)
{
	// Token invalid, show empty page
	return $page->build("", "applicationFramePlayer", TRUE)->getReport();
}

// Get application id
$appID = engine::getVar('id');
$appName = engine::getVar('name');
$app = new DEVApplication($appID, $appName);
$appID = $app->getID();

// Build application player
$page->build("", "applicationFramePlayer", TRUE);

// Update application version (if there is a next version to update)
appMarket::updateTeamAppVersion($appID);
$teamAppVersion = appMarket::getTeamAppVersion($appID);

// De-activate tester mode for application
appTester::setPublisherLock(TRUE, "boss");


// LOAD APP RESOURCSE

// Application source resources
$cssUrl = BootLoader::getResourceUrl(BootLoader::RSRC_CSS, $appID, $appID, DEVApplication::PUB_RSRC_SOURCE, $teamAppVersion);
$jsUrl = BootLoader::getResourceUrl(BootLoader::RSRC_JS, $appID, $appID, DEVApplication::PUB_RSRC_SOURCE, $teamAppVersion);
$header = BootLoader::getResourceArray(DEVApplication::PROJECT_TYPE, "EnpApplicationSource", $appID, $cssUrl, $jsUrl, $tester = FALSE);
$page->addResourceHeader($header['id'], $header);

// Application library resources
$styles = application::getAppLibraryStyles($appID, $teamAppVersion);
foreach ($styles as $styleName)
{
	$cssUrl = BootLoader::getResourceUrl(BootLoader::RSRC_CSS, $appID, DEVApplication::PUB_RSRC_LIBRARY, $styleName, $teamAppVersion);
	$header = BootLoader::getResourceArray(DEVApplication::PROJECT_TYPE, "EnpApplicationLibraryCSS", $appID, $cssUrl, NULL, $tester = FALSE);
	$page->addResourceHeader($header['id'], $header);
}

$scripts = application::getAppLibraryStyles($appID, $teamAppVersion);
foreach ($scripts as $scriptName)
{
	$jsUrl = BootLoader::getResourceUrl(BootLoader::RSRC_JS, $appID, DEVApplication::PUB_RSRC_LIBRARY, $scriptName, $teamAppVersion);
	$header = BootLoader::getResourceArray(DEVApplication::PROJECT_TYPE, "EnpApplicationLibraryJS", $appID, NULL, $jsUrl, $tester = FALSE);
	$page->addResourceHeader($header['id'], $header);
}


// COMPATIBILITY
$cssUrl = BootLoader::getResourceUrl(BootLoader::RSRC_CSS, $appID, "Apps", $appID, $teamAppVersion);
$jsUrl = BootLoader::getResourceUrl(BootLoader::RSRC_JS, $appID, "Apps", $appID, $teamAppVersion);
$header = BootLoader::getResourceArray(DEVApplication::PROJECT_TYPE, "EnpApplication", $appID, $cssUrl, $jsUrl, $tester = FALSE);
$page->addResourceHeader($header['id'], $header);


// LOAD APP VIEW

// Get initial application view
appPlayer::init($appID);
$appOutput = appPlayer::loadView();

// Remove application content container
$appContentContainer = HTML::select(".".APPContent::CONTAINER_CLASS)->item(0);
DOM::replace($appContentContainer, NULL);


// Parse report and get actions
HTMLServerReport::parseReportContent($appOutput, $defaultHolder = APPContent::HOLDER, $actions);

// Parse actions
foreach ($actions as $key => $action)
{
	$pageHelper = DOM::find("pageHelper");
	$actionContainer = DOM::create("span", "", "", "actionContainer");
	DOM::data($actionContainer, "action", $action);
	DOM::append($pageHelper, $actionContainer);
}

// Add switch application action
$page->addReportAction("application.switch", $appID);

// Return output
return $page->getReport();
//#section_end#
?>