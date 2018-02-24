<?php
//#section#[header]
// Module Declaration
$moduleID = 302;

// Inner Module Codes
$innerModules = array();
$innerModules['register'] = 67;

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
importer::import("API", "Security");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("ESS", "Protocol");
importer::import("ESS", "Environment");
importer::import("UI", "Content");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \ESS\Protocol\api\APIResponse;
use \API\Profile\account;
use \API\Resources\forms\inputValidator;
use \API\Security\akeys\apiKey;
use \UI\Content\JSONContent;

// Create Response Content
$respContent = new APIResponse();

// Validate api call
$appID = engine::getVar('app_id');
$akey = engine::getVar('akey');
if (!apiKey::validateKey($akey, $accountID = NULL, $teamID = NULL, $projectID = $appID))
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

// Check method
if (!engine::isPost())
{
	// Invalid method
	$respContent->setStatus(0);

	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: Invalid Request. Your request method is not correct for this action.";
	$respContent->addContent($response, "registration");

	// Return response
	return $respContent->getResponse();
}

// Initialize error list
$errorList = array();
$has_error = FALSE;

// Check Firstname
if (empty($_POST['firstname']))
{
	$has_error = TRUE;
	$errorList[] = "Firstname is empty.";
}

// Check Lastname
if (empty($_POST['lastname']))
{
	$has_error = TRUE;
	$errorList[] = "Lastname is empty.";
}

// Check Email
$empty = empty($_POST['email']);
$valid = inputValidator::checkEmail($_POST['email']);
if ($empty || !$valid)
{
	$has_error = TRUE;
	$errorList[] = "Email is not valid.";
}

// Check Email match
$match = ($_POST['email'] == $_POST['email2']);
if (!$match)
{
	$has_error = TRUE;
	$errorList[] = "Emails don't match.";
}

// Check Password
if (empty($_POST['password']))
{
	$has_error = TRUE;
	$errorList[] = "Password is empty.";
}

// If error, return response
if ($has_error)
{
	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: Error in registration. See the error list for more details.";
	$response['errors'] = $errorList;
	$respContent->addContent($response, "registration");

	// Return response
	return $respContent->getResponse();
}

// Create account
$status = account::getInstance()->create($_POST['email'], $_POST['firstname'], $_POST['lastname'], $_POST['password']);
if (!$status)
{
	$response = array();
	$response['status'] = 0;
	$response['description'] = "ERROR: Error in registration. This may be caused by two reasons: email is invalid or already user, the service is temporarily unavailable. Please try again later.";
	$respContent->addContent($response, "registration");

	// Return response
	return $respContent->getResponse();
}

// Create response
$response = array();
$response['status'] = 1;
$response['description'] = "Your Drovio account has been created.";

// Login account
if (engine::getVar("login"))
{
	$username = engine::getVar("email");
	$password = engine::getVar("password");
	$status = account::getInstance()->login($username, $password, FALSE);

	// Create response
	$response['auth_token'] = account::getInstance()->getAuthToken();
	$response['info'] = account::getInstance()->info();
}

// Set response content
$respContent->addContent($response, "registration");

// Return response
return $respContent->getResponse();
//#section_end#
?>