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
importer::import("API", "Security");
importer::import("DEV", "Apps");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \ESS\Protocol\loaders\AppLoader;
use \ESS\Protocol\reports\JSONServerReport;
use \API\Security\accountKey;
use \DEV\Apps\application as DEVApplication;
use \DEV\Apps\test\appTester;
use \DEV\Apps\test\viewTester;
use \DEV\Apps\test\sourceTester;

// Create Response Content
$respContent = new APIResponse();

// Get application info
$appID = engine::getVar('app_id');
$appName = engine::getVar('app_name');
$application = new DEVApplication($appID, $appName);
$appID = $application->getID();

// Validate api call
$akey = engine::getVar('akey');
if (!accountKey::validate($akey, $appID, 2))
{
	// Invalid key
	$respContent->setStatus(0);
	
	// Create response
	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: Key is invalid or doesn't correspond to the given application id.";
	$respContent->addContent($response, "application");
	
	// Return response
	return $respContent->getResponse();
}

// Get or set tester mode levels
// Temporary activate everything
viewTester::activate($appID);
sourceTester::activate($appID);


// Activate tester mode for application
appTester::setPublisherLock(FALSE);

// Get view name to load and return application view output
$viewName = engine::getVar('app_vn');
$applicationDataJSON = AppLoader::load($appID, $viewName);
JSONServerReport::parseReportContent($applicationDataJSON, $actions = array(), $applicationDataArray = array());
foreach ($applicationDataArray[JSONServerReport::CONTENT_JSON] as $key => $appData)
	$respContent->addContent($appData, $key);

// Return response
return $respContent->getResponse();
//#section_end#
?>