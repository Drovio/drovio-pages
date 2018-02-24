<?php
//#section#[header]
// Module Declaration
$moduleID = 303;

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
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Content");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \ESS\Environment\url;
use \API\Profile\account;

// Create Response Content
$respContent = new APIResponse();

// Validate account
$response = array();
$response['status'] = (account::validate() ? 1 : 0);
if (!account::getInstance()->validate())
{
	// Set redirect
	$respContent->setRedirect(url::resolve("api", "/account/login.php"));
	
	// Add response description
	$response['description'] = "Your are not logged in.";
	$respContent->addContent($response, "account");
	
	// Return response
	return $respContent->getResponse();
}

// Add account info
$response['auth_token'] = account::getInstance()->getAuthToken();
$response['info'] = account::getInstance()->info();
$respContent->addContent($response, "account");

// Return response
return $respContent->getResponse();
//#section_end#
?>