<?php
//#section#[header]
// Module Declaration
$moduleID = 301;

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

if (engine::isPost())
{
	// Validate account
	if (account::validate())
	{
		// Set redirect
		$respContent->setRedirect(url::resolve("api", "/account/info.php"));
		
		// Create response
		$response = array();
		$response['status'] = 1;
		$response['description'] = "You are already logged in.";
		$respContent->addContent($response, "login");
		
		// Return response
		return $respContent->getResponse();
	}

	// Get credentials
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// Login account
	$status = account::getInstance()->login($username, $password, FALSE);
	
	// Create response
	$response = array();
	$response['status'] = $status;
	if ($status)
	{
		$response['auth_token'] = account::getInstance()->getAuthToken();
		$response['info'] = account::getInstance()->info();
	}
	$respContent->addContent($response, "login");
	
	// Return response
	return $respContent->getResponse();
}

// Invalid method
$respContent->setStatus(0);

$response = array();
$response['status'] = 0;
$response['description'] = "ERROR: Invalid Request. Your request method is not correct for this action.";
$respContent->addContent($response, "login");

// Return response
return $respContent->getResponse();
//#section_end#
?>