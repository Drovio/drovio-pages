<?php
//#section#[header]
// Module Declaration
$moduleID = 297;

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
importer::import("AEL", "Security");
importer::import("API", "Security");
importer::import("DEV", "Apps");
importer::import("DEV", "Profiler");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \ESS\Protocol\reports\JSONServerReport;
use \AEL\Platform\application as appPlayer;
use \AEL\Profiler\logger as appLogger;
use \AEL\Security\privateKey;
use \AEL\Security\publicKey;
use \API\Security\akeys\apiKey;
use \DEV\Apps\application as DEVApplication;
use \DEV\Apps\test\appTester;
use \DEV\Apps\test\viewTester;
use \DEV\Apps\test\sourceTester;
use \DEV\Profiler\debugger;

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
	// Set response status code
	$respContent->setResponseCode($code = 401);
	
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

// Get or set tester mode levels
// Temporary activate everything
viewTester::activate($appID);
sourceTester::activate($appID);

// Activate tester mode for application
appTester::setPublisherLock(FALSE);
appPlayer::init($appID);

// Validate private or public app key
$teamID = privateKey::getTeamID($akey);
$hostOrigin = engine::getVar("origin");
if (!(privateKey::validate($akey, $teamID) || (publicKey::validate($akey, $teamID) && publicKey::validateOrigin($akey, $hostOrigin))))
{
	// Set response status code
	$respContent->setResponseCode($code = 401);
	
	// Invalid key
	$respContent->setStatus(0);
	
	// Create response
	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: API key is not valid or registered in the team settings.";
	$respContent->addContent($response, "api_error");
	
	// Log error
	mlogger::getInstance()->log("ERROR: API key is not valid or registered in the team settings.", mlogger::ERROR, $_REQUEST);
	
	// Return response
	return $respContent->getResponse();
}

// Add application header info
$appInfo = array();
$appInfo['id'] = $applicationInfo['id'];
$appInfo['name'] = $applicationInfo['name'];
$appInfo['version'] = "dev";
$respContent->addHeader($appInfo, $key = "app_info");

// Get view name to load and return application view output
$viewName = engine::getVar('app_vn');
try
{
	$applicationDataJSON = appPlayer::loadView($viewName);
	$parsedReport = JSONServerReport::parseReportContent($applicationDataJSON, $actions = array(), $applicationDataArray = array(), $applicationHeaderArray = array());
	if (empty($applicationHeaderArray))
		$applicationHeaderArray = $parsedReport['head'];
	foreach ($applicationDataArray[JSONServerReport::CONTENT_JSON] as $key => $appData)
		$respContent->addContent($appData, $key);
}
catch (Exception $ex)
{
	// Log error
	appLogger::getInstance()->log("[API/DEV] An exception occurred while trying to load application view [".$appID."]->[".$viewName."]: ".$ex->getMessage(), appLogger::ERROR, $_REQUEST);
	
	// Set response status code
	$respContent->setResponseCode($code = 500);
	
	// Add error
	$error = array();
	$error['message'] = "An exception occurred while trying to load application view [".$appID."]->[".$viewName."]. See 'exception' field for more details.";
	$respContent->addContent($error, "error");
	
	// Add exception
	$exception = array();
	$exception['code'] = $ex->errCode;
	if (debugger::status())
	{
		$exception['message'] = $ex->getMessage();
		$exception['backtrace'] = $ex->getTrace();
	}
	$respContent->addContent($exception, "exception");
}

// Return response
return $respContent->getResponse($allowOrigin = $hostOrigin, $withCredentials = $applicationHeaderArray['Access-Control-Allow-Credentials']);
//#section_end#
?>