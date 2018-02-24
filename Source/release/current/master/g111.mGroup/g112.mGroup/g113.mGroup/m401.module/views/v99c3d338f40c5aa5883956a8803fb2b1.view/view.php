<?php
//#section#[header]
// Module Declaration
$moduleID = 401;

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
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \AEL\Platform\application as appPlayer;
use \AEL\Security\publicKey;
use \API\Model\modules\module;
use \DEV\Apps\application as DEVApplication;

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

// Set header
header('Content-type: text/javascript');

// Create js response
$jsResponse = "";

// Initialize variables for the javascript code
$allRequestVars = $_GET;
$allRequestVars['app_id'] = $appID;
$allRequestVars['app_name'] = $appName;
$jsResponse .= "// Initialize script variables\n";
$jsResponse .= "var ";
foreach ($allRequestVars as $name => $value)
	if (!empty($value))
		$jsResponse .= $name." = '".$value."', ";

$jsResponse = trim($jsResponse, ", ");
$jsResponse .= ";\n";

// Load javascript pre-loader
$jsResponse .= "\n";
$jsResponse .= module::getJS($moduleID, "ApplicationJSPreLoader");

// Return js response
return $jsResponse;
//#section_end#
?>