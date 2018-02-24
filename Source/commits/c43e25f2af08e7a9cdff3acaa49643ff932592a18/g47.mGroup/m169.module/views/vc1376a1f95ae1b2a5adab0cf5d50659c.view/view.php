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
importer::import("API", "Model");
importer::import("UI", "Modules");
importer::import("UI", "Apps");
importer::import("ESS", "Protocol");
importer::import("AEL", "Platform");
importer::import("DEV", "Projects");
importer::import("DEV", "Apps");
importer::import("DEV", "Resources");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \ESS\Protocol\BootLoader;
use \API\Model\modules\module;
use \AEL\Platform\application as appPlayer;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get application id
$appID = $_GET['id'];
$app = new application($appID);
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
foreach ($output['body'] as $body)
{
	$type = $body['type'];
	$context = $body['context'];
	if ($type == "action")
	{
		$pageHelper = DOM::find("pageHelper");
		$actionContainer = DOM::create("span", "", "", "actionContainer");
		DOM::data($actionContainer, "action", $body['context']);
		DOM::append($pageHelper, $actionContainer);
	}
	else if ($type == "data")
	{
		// Get method and holder
		$method = $body['method'];
		$holder = $body['holder'];
		// Get holder and append context
		if (!empty($holder))
			$holderElement = HTML::select($holder)->item(0);
		
		if (empty($holder) && empty($holderElement))
			$holderElement = HTML::select(APPContent::HOLDER)->item(0);
		
		DOM::innerHTML($applicationContainer, $context);
	}
}

// Add extra application resource headers
$header = array();
$cssUrl = projectLibrary::getPublishedPath($appID, $appVersion)."/style.css";
$cssUrl = str_replace(paths::getPublishedPath(), "", $cssUrl);
$header['css'] = url::resolve("lib", $cssUrl);
$jsUrl = projectLibrary::getPublishedPath($appID, $appVersion)."/script.js";
$jsUrl = str_replace(paths::getPublishedPath(), "", $jsUrl);
$header['js'] = url::resolve("lib", $jsUrl);
$rsrcID = BootLoader::getRsrcID("Applications", $appID);
$page->addResourceHeader($rsrcID, $header);

// Add switch application action
$page->addReportAction("application.switch", $appID);

// Return output
return $page->getReport();
//#section_end#
?>