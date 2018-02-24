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
importer::import("BSS", "Dashboard");
importer::import("DEV", "Apps");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
importer::import("UI", "Apps");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \AEL\Platform\application as appPlayer;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\notification;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;
use \BSS\Dashboard\appLibrary;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get application id
$appID = engine::getVar('appID');
$app = new application($appID);
$appID = $app->getID();


// Update application version (if there is a next version to update)
projectLibrary::updateTeamProjectVersion($appID);

// Load the application

// De-activate tester mode for application
appTester::setPublisherLock(TRUE, "boss");

// Get initial application view
appPlayer::init($appID);
$appOutput = appPlayer::loadView();



// Build application player
// Build the module for a valid application
$pageContent->build("", "applicationPlayer", TRUE);


// Check for new version of the application and show top notification
$lastAppVersion = projectLibrary::getLastProjectVersion($appID);
$teamAppVersion = appLibrary::getTeamAppVersion($appID);
if (version_compare($lastAppVersion, $teamAppVersion, ">"))
{
	// Set status container
	$appPlayerWrapper = HTML::select(".applicationPlayer .appPlayerWrapper")->item(0);
	HTML::addClass($appPlayerWrapper, "withStatus");
	
	// Insert application updater
	$appStatus = HTML::select(".applicationPlayer .appPlayerWrapper .appStatus")->item(0);
	$appUpdater = module::loadView($moduleID, "applicationUpdater");
	DOM::append($appStatus, $appUpdater);
}

// Set data application
$pageContainer = $pageContent->get();
$appData = array();
$appData['id'] = $appID;
HTML::data($pageContainer, "app", $appData);


// Handle application output and append
$output = json_decode($appOutput, TRUE);
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
			
			// Break
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
$cssUrl = projectLibrary::getPublishedPath($appID, $teamAppVersion)."/style.css";
$cssUrl = str_replace(paths::getPublishedPath(), "", $cssUrl);
$cssUrl = url::resolve("lib", $cssUrl);
$jsUrl = projectLibrary::getPublishedPath($appID, $teamAppVersion)."/script.js";
$jsUrl = str_replace(paths::getPublishedPath(), "", $jsUrl);
$jsUrl = url::resolve("lib", $jsUrl);

// Add Header
$header = BootLoader::getResourceArray(4, "BOSSApplication", $appID, $cssUrl, $jsUrl, $tester = FALSE);
$pageContent->addResourceHeader($header['id'], $header);

// Add switch application action
$pageContent->addReportAction("application.switch", $appID);

// Return output
return $pageContent->getReport(".apps_pool", "append");
//#section_end#
?>