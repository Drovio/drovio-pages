<?php
//#section#[header]
// Module Declaration
$moduleID = 137;

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
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Apps");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\loaders\AppLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get application id
$appID = engine::getVar('id');
$appName = engine::getVar('name');
$app = new application($appID, $appName);
$appID = $app->getID();

// Build the module for a valid application
$applicationContainer = $pageContent->build("applicationContainer", "applicationTester")->get();

// Activate tester mode for application
appTester::setPublisherLock(FALSE);

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
$params = array();
$params['id'] = $appID;
$cssUrl = BootLoader::getTesterResourceUrl("/ajax/apps/css.php", "Application", $appID);
$jsUrl = BootLoader::getTesterResourceUrl("/ajax/apps/js.php", "Application", $appID);

// Add Header
$header = BootLoader::getResourceArray(4, "ApplicationTester", $appID, $cssUrl, $jsUrl, $tester = TRUE);
$pageContent->addResourceHeader($header['id'], $header);

// Return output
return $pageContent->getReport(APPContent::HOLDER);
//#section_end#
?>