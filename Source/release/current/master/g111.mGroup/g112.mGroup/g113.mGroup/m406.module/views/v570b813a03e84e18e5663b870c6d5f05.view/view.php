<?php
//#section#[header]
// Module Declaration
$moduleID = 406;

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
$hostOrigin = engine::getVar("origin");
if (!(publicKey::validate($akey, $teamID) && publicKey::validateOrigin($akey, $hostOrigin)))
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
appTester::setPublisherLock(FALSE);

// Get application css style file
$cssFileName = engine::getVar("app_css");

// Set header
header('Content-type: text/css');
echo "@charset \"UTF-8\";\n";

// Load application library css style file
return application::getApplicationLibraryCSS($appID, $cssFileName, $applicationVersion = "");
//#section_end#
?>