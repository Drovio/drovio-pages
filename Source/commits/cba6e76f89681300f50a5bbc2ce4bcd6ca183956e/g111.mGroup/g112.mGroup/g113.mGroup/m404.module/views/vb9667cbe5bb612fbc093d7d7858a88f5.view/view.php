<?php
//#section#[header]
// Module Declaration
$moduleID = 404;

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
importer::import("AEL", "Security");
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \AEL\Platform\application as appPlayer;
use \AEL\Security\publicKey;
use \API\Model\apps\application;
use \DEV\Apps\application as DEVApplication;
use \DEV\Apps\test\appTester;

// Get application info
$appID = engine::getVar('app_id');
$appName = engine::getVar('app_name');
$application = new DEVApplication($appID, $appName);
$applicationInfo = $application->info();
$appID = $application->getID();

// Init application
appPlayer::init($appID);

// Validate api call
$akey = engine::getVar('akey');
$teamID = publicKey::getTeamID($akey);
if (!publicKey::validate($akey, $teamID))
{
	// Load invalid key
	$respContent = new APIResponse();
	
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

// Activate tester mode for application
appTester::setPublisherLock(TRUE);

// Get application javascript file
$jsFileName = engine::getVar("app_js");

// Set header
header('Content-type: text/javascript');

// Load application library javascript file
return application::getApplicationLibraryJS($appID, $jsFileName, $applicationVersion = "");
//#section_end#
?>