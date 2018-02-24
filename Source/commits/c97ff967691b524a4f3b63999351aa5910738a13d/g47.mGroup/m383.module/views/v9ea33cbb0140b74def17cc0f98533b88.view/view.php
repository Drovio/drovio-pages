<?php
//#section#[header]
// Module Declaration
$moduleID = 383;

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
importer::import("DEV", "Apps");
importer::import("DEV", "Profile");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("SYS", "Comm");
importer::import("UI", "Apps");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
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
use \DEV\Apps\test\appTester;
use \DEV\Apps\application as DEVApp;
use \DEV\Profile\team as devTeam;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get token and validate
$token = engine::getVar("token");
$sessionToken = session::get("app_center_frame_player_token", NULL, $namespace = "application_center");
if ($token != $sessionToken)
{
	// Token invalid, show empty page
	return $page->build("", "applicationFramePlayer", TRUE)->getReport();
}

// Get application id
$appID = engine::getVar('id');
$appName = engine::getVar('name');
$app = new DEVApp($appID, $appName);
$appID = $app->getID();

// Get application info
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "app_info");
$attr = array();
$attr['app_id'] = $appID;
$result = $dbc->execute($q, $attr);
$appInfo = $dbc->fetch($result);

// Build the module for a valid application
$page->build($appInfo['title'], "applicationFramePlayer", TRUE);
$applicationContainer = HTML::select(".applicationFramePlayer #applicationContainer")->item(0);

// Load the application
$appLastVersion = appSessionManager::getInstance()->getLastVersion($appID);
$appVersion = appSessionManager::getInstance()->getVersion($appID);
if (!empty($appLastVersion) && $appVersion != $appLastVersion)
{
	appSessionManager::getInstance()->setVersion($appID, $appLastVersion);
	$appVersion = $appLastVersion;
}
// If there is no version active, return empty page
if (empty($appVersion))
{
	// Return output
	return $page->getReport();
}

// Validate that application is for application center
$q = module::getQuery($moduleID, "get_appcenter_app");
$attr = array();
$attr['app_id'] = $appID;
$result = $dbc->execute($q, $attr);
if ($dbc->get_num_rows($result) == 0)
{
	// Return output
	return $page->getReport();
}

// Set data application
$pageContainer = $page->get();
$appData = array();
$appData['id'] = $appID;
HTML::data($pageContainer, "app", $appData);

// De-activate tester mode for application
appTester::setPublisherLock(TRUE, "apps");

// Get initial application view
$appOutput = AppLoader::load($appID);
$output = json_decode($appOutput, TRUE);

// Remove application content container
$appContentContainer = HTML::select(".".APPContent::CONTAINER_CLASS)->item(0);
DOM::replace($appContentContainer, NULL);

// Parse report and get actions
HTMLServerReport::parseReportContent($output, $defaultHolder = APPContent::HOLDER, $actions);

// Parse actions
foreach ($actions as $key => $action)
{
	$pageHelper = DOM::find("pageHelper");
	$actionContainer = DOM::create("span", "", "", "actionContainer");
	DOM::data($actionContainer, "action", $action);
	DOM::append($pageHelper, $actionContainer);
}

// Get application resources
$cssUrl = BootLoader::getResourceUrl(BootLoader::RSRC_CSS, $appID, "Apps", $appID, $appVersion);
$jsUrl = BootLoader::getResourceUrl(BootLoader::RSRC_JS, $appID, "Apps", $appID, $appVersion);

// Add header
$header = BootLoader::getResourceArray(4, "Application", $appID, $cssUrl, $jsUrl, $tester = FALSE);
$page->addResourceHeader($header['id'], $header);


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

// Return output
return $page->getReport();
//#section_end#
?>