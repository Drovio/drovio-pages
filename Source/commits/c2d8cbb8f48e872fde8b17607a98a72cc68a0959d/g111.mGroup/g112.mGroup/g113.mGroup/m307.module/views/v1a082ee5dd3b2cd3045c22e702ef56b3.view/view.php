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
importer::import("UI", "Content");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\loaders\AppLoader;
use \API\Security\accountKey;
use \UI\Content\JSONContent;

// Validate api call
$appID = engine::getVar('app_id');
$akey = engine::getVar('akey');
if (!accountKey::validate($akey, $appID, 2))
{
	// Create Error Response Content
	$respContent = new JSONContent();
	
	// Add header
	$header = array();
	$header['status'] = 0;
	$header['description'] = "ERROR: Key is invalid or doesn't correspond to the given application id.";
	$respContent->addHeader($header);
	
	$response = array();
	$response['description'] = "ERROR: Key is invalid or doesn't correspond to the given application id.";
	
	// Return response
	return $respContent->getReport($response, "*", FALSE, "result");
}

// Activate tester mode for application
appTester::setPublisherLock(TRUE);

//Get view name to load and return application view output
$viewName = engine::getVar('app_vn');
return AppLoader::load($appID, $viewName);
//#section_end#
?>