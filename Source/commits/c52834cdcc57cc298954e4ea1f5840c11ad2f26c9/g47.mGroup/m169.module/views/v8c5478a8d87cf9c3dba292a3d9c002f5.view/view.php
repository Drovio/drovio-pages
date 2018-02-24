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
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("SYS", "Comm");
importer::import("UI", "Apps");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \API\Model\modules\module;
use \API\Model\apps\application;
use \AEL\Platform\application as appPlayer;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application as DEVApp;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

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
$page->build($appInfo['title'], "applicationPlayer", TRUE);
$applicationContainer = HTML::select(".applicationPlayer #applicationContainer")->item(0);

// Load the application
$appVersion = projectLibrary::getLastProjectVersion($appID);
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
appPlayer::init($appID);
$appOutput = appPlayer::loadView();
$output = json_decode($appOutput, TRUE);

// Remove application content container
$appContentContainer = HTML::select(".".APPContent::CONTAINER_CLASS)->item(0);
DOM::replace($appContentContainer, NULL);

// Fetch body content
foreach ($output['body'] as $body)
{
	$type = $body['type'];
	$context = $body['context'];
	switch ($type)
	{
		case HTMLServerReport::CONTENT_ACTION:
			$pageHelper = DOM::find("pageHelper");
			$actionContainer = DOM::create("span", "", "", "actionContainer");
			DOM::data($actionContainer, "action", $body['context']);
			DOM::append($pageHelper, $actionContainer);
			
			// Break action
			break;
		case HTMLServerReport::CONTENT_DATA:
		case HTMLServerReport::CONTENT_HTML:
			// Get method and holder
			$method = $body['method'];
			$holder = $body['holder'];
			
			// Get holder and append context
			$holder = (empty($holder) ? APPContent::HOLDER : $holder);
			$holderElement = HTML::select($holder)->item(0);
			// Select method of append
			switch ($method)
			{
				case HTMLServerReport::APPEND_METHOD:
					$oldInnerHTML = DOM::innerHTML($holderElement);
					$newInnerHTML = $oldInnerHTML.$context;
					DOM::innerHTML($holderElement, $newInnerHTML);
					break;
				case HTMLServerReport::REPLACE_METHOD:
					DOM::innerHTML($holderElement, $context);
			}
	}
}

// Get application resources
$cssUrl = projectLibrary::getPublishedPath($appID, $appVersion)."/style.css";
$cssUrl = str_replace(paths::getPublishedPath(), "", $cssUrl);
$cssUrl = url::resolve("lib", $cssUrl);
$jsUrl = projectLibrary::getPublishedPath($appID, $appVersion)."/script.js";
$jsUrl = str_replace(paths::getPublishedPath(), "", $jsUrl);
$jsUrl = url::resolve("lib", $jsUrl);

// Add header
$header = BootLoader::getResourceArray(4, "Application", $appID, $cssUrl, $jsUrl, $tester = FALSE);
$page->addResourceHeader($header['id'], $header);

// Return output
return $page->getReport();
//#section_end#
?>