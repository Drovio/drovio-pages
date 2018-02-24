<?php
//#section#[header]
// Module Declaration
$moduleID = 307;

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
importer::import("AEL", "Profiler");
importer::import("API", "Security");
importer::import("DEV", "Apps");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \ESS\Protocol\reports\JSONServerReport;
use \AEL\Platform\application as appPlayer;
use \AEL\Profiler\logger as appLogger;
use \DEV\Apps\application as DEVApplication;
use \DEV\Apps\test\appTester;
use \API\Security\akeys\apiKey;

// Create Response Content
$respContent = new APIResponse();

// Get application info
$appID = engine::getVar('app_id');
$appName = engine::getVar('app_name');
$application = new DEVApplication($appID, $appName);
$applicationInfo = $application->info();
$appID = $application->getID();

// Validate api call
$akey = engine::getVar('akey');
if (!apiKey::validateProjectKey($akey, $appID))
{
	// Invalid key
	$respContent->setStatus(0);
	
	// Create response
	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: Key is invalid or doesn't correspond to the given application id.";
	$respContent->addContent($response, "api_error");
	
	// Log error
	mlogger::getInstance()->log("ERROR: Key is invalid or doesn't correspond to the given application id.", mlogger::ERROR, $_REQUEST);
	
	// Return response
	return $respContent->getResponse();
}

// Add application header info
$appInfo = array();
$appInfo['id'] = $applicationInfo['id'];
$appInfo['name'] = $applicationInfo['name'];
$appInfo['version'] = appPlayer::getApplicationVersion($applicationInfo['id']);
$respContent->addHeader($appInfo, $key = "app_info");

// Activate tester mode for application
appTester::setPublisherLock(TRUE);

// Get view name to load and return application view output
$viewName = engine::getVar('app_vn');
try
{
	appPlayer::init($appID);
	$applicationDataJSON = appPlayer::loadView($viewName);
	JSONServerReport::parseReportContent($applicationDataJSON, $actions = array(), $applicationDataArray = array());
	foreach ($applicationDataArray[JSONServerReport::CONTENT_JSON] as $key => $appData)
		$respContent->addContent($appData, $key);
}
catch (Exception $ex)
{
	// Log error
	appLogger::getInstance()->log("[API/PRODUCTION] An exception occurred while trying to load application view [".$appID."]->[".$viewName."].", appLogger::ERROR, $_REQUEST);
	
	// Add error
	$error = array();
	$error['message'] = "An exception occurred while trying to load application view [".$appID."]->[".$viewName."]. See 'exception' field for more details.";
	$respContent->addContent($error, "error");
	
	// Add exception
	$exception = array();
	$exception['code'] = $ex->errCode;
	$respContent->addContent($exception, "exception");
}

// Return response
return $respContent->getResponse();
//#section_end#
?>