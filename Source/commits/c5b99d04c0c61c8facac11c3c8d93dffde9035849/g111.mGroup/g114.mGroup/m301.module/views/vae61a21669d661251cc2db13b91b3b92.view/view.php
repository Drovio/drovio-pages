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

if (engine::isPost())
{
	// Validate account
	if (account::validate())
	{
		// Add header
		$header = array();
		$header['status'] = 1;
		$header['description'] = "You are already logged in.";
		$header['redirect'] = url::resolve("api", "/account/info.php");
		$respContent->addHeader($header, "result");
		
		// Create response
		$response = array();
		$response['description'] = "You are already logged in.";
		
		// Return response
		return $respContent->getReport($response, "*", FALSE, "result");
	}

	// Get credentials
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// Login account
	$status = account::login($username, $password, FALSE);
	
	// Check login status
	if ($status)
	{
		// Get default team and switch to that
		$defaultTeam = team::getDefaultTeam();
		$defaultTeamID = $defaultTeam['id'];
		if (isset($defaultTeamID))
			team::switchTeam($defaultTeamID, $password);
	}
	
	// Create response
	$response = array();
	$response['status'] = $status;
	if ($status)
		$response['account'] = getAccountInfo();
	
	// Return response
	return $respContent->getReport($response, "*", FALSE, "result");
}

// Invalid method
// Add header
$header = array();
$header['status'] = 0;
$header['description'] = "ERROR: Invalid Request.";
$respContent->addHeader($header, "result");

$response = array();
$response['description'] = "ERROR: Invalid Request. Your request method is not correct for this action.";

// Return response
return $respContent->getReport($response, "*", FALSE, "result");


// Get specific account info for account validation in all API requests
function getAccountInfo()
{
	$info = array();
	$info['acc'] = account::getAccountID();
	$info['mx'] = account::getMX();
	$info['ssid'] = account::getSessionID();
	
	// Team
	$teamID = team::getTeamID();
	if (!empty($teamID))
		$info['tm'] = $teamID;
	
	// Person
	$personID = account::getPersonID();
	if (!empty($personID))
		$info['person'] = $personID;
	
	return $info;
}
//#section_end#
?>