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
importer::import("ESS", "Environment");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\team;
use \API\Profile\account;
use \UI\Content\JSONContent;

// Create Response Content
$respContent = new JSONContent();

// Validate account
if (!account::validate())
{
	// Add header
	$header = array();
	$header['status'] = 0;
	$header['description'] = "Your are not logged in.";
	$header['redirect'] = url::resolve("api", "/account/login.php");
	$respContent->addHeader($header);
	
	$response = array();
	$response['description'] = "Your are not logged in.";
	
	// Return response
	return $respContent->getReport($response, "*", FALSE);
}



// Create response
$response = array();
$response['account'] = getAccountInfo();
$response['sessions'] = getSessionsInfo();

// Return response
return $respContent->getReport($response, "*", FALSE);



// Get specific account info for account validation in all API requests
function getAccountInfo()
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
function getSessionsInfo()
{
	$sessions = array();
	
	return $sessions;
}
//#section_end#
?>