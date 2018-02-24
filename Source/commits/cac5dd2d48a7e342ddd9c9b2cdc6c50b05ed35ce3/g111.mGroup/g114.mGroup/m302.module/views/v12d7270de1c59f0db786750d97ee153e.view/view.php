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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Content");
importer::import("ESS", "Protocol");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \ESS\Protocol\api\APIResponse;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\team;
use \API\Profile\person;
use \API\Resources\forms\inputValidator;
use \API\Security\accountKey;
use \UI\Content\JSONContent;

// Create Response Content
$respContent = new APIResponse();

// Validate api call
$appID = engine::getVar('app_id');
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

// Register user
if (engine::isPost())
{
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
	
	// Initialize dbConnection
	$dbc = new dbConnection();
	
	// Check if there is a non-activated account with the provided email.
	$dbq = module::getQuery($innerModules['register'], "get_nonactivated_person");
	
	// Set attributes
	$attr = array();
	$attr["firstname"] = $_POST['firstname'];
	$attr["lastname"] = $_POST['lastname'];
	$attr["password"] = hash("SHA256", $_POST['password']);
	$attr['accountTitle'] = $_POST['firstname']." ".$_POST['lastname'];
	$attr["email"] = $_POST['email'];
	
	// Get non activated person
	$result = $dbc->execute($dbq, $attr);
	if ($dbc->get_num_rows($result) > 0)
	{
		// Register Existing Person
		$person = $dbc->fetch($result);
		$attr['pid'] = $person['id'];
		
		$dbq = module::getQuery($innerModules['register'], "register_update");
		$result = $dbc->execute($dbq, $attr);
	}
	else
	{
		// Register New Person
		$dbq = module::getQuery($innerModules['register'], "register");
		$result = $dbc->execute($dbq, $attr);
	}
	
	// If there is an error in registration, show it
	if (!$result)
	{
		$response = array();
		$response['status'] = 0;
		$response['description'] = "ERROR: Error in registration. This may be caused by two reasons: email is invalid or already user, the service is temporarily unavailable. Please try again later.";
		$respContent->addContent($response, "registration");
		
		// Return response
		return $respContent->getResponse();
	}
	
	// Login account
	$username = engine::getVar("email");
	$password = engine::getVar("password");
	$status = account::login($username, $password, FALSE);
	
	// Create response
	
	// Set redirect
	if ($status)
		$respContent->setRedirect(url::resolve("api", "/account/info.php"));
	
	$response = array();
	$response['status'] = 1;
	$response['description'] = "Your Redback account has been created.";
	if ($status)
		$response['session'] = getAccountSession();
	$respContent->addContent($response, "registration");
	
	// Return response
	return $respContent->getResponse();
}

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

// Invalid method
$respContent->setStatus(0);

$response = array();
$response['status'] = 0;
$response['description'] = "ERROR: Invalid Request. Your request method is not correct for this action.";
$respContent->addContent($response, "registration");

// Return response
return $respContent->getResponse();
//#section_end#
?>