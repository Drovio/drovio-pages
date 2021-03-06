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
importer::import("API", "Profile");
importer::import("UI", "Content");
importer::import("ESS", "Protocol");
importer::import("ESS", "Environment");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \ESS\Environment\url;
use \API\Profile\team;
use \API\Profile\account;
use \API\Profile\person;

// Create Response Content
$respContent = new APIResponse();

// Validate account
$response = array();
$response['status'] = (account::validate() ? 1 : 0);
if (!account::validate())
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
$response['session'] = getAccountSession();
$response['info'] = getAccountInfo();
$respContent->addContent($response, "account");

// Return response
return $respContent->getResponse();



// Get specific account info for account validation in all API requests
function getAccountSession()
{
	$info = array();
	$info['acc'] = account::getAccountID();
	$info['mx'] = account::getMX();
	$info['ssid'] = account::getSessionID();
	
	// Team
	$info['tm'] = team::getTeamID();
	
	// Person
	$personID = account::getPersonID();
	if (!empty($personID))
		$info['person'] = $personID;
	
	return $info;
}

// Get all account active sessions
function getAccountInfo()
{
	$info = array();
	
	// Get account info
	$acc_info = account::info();
	$person_info = person::info();
	$info = array_merge($acc_info, $person_info);
	unset($info['id']);
	
	return $info;
}
//#section_end#
?>