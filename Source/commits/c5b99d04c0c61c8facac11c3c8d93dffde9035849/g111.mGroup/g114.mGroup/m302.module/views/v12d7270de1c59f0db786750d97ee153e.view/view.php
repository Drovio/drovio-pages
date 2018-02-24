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
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Resources\forms\inputValidator;
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
		// Add header
		$header = array();
		$header['status'] = 0;
		$header['description'] = "ERROR: Error in registration.";
		$respContent->addHeader($header, "result");
		
		$response = array();
		$response['description'] = "ERROR: Error in registration. See the error list for more details.";
		$response['errors'] = $errorList;
		
		// Return response
		return $respContent->getReport($response, "*", FALSE, "result");
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
		// Add header
		$header = array();
		$header['status'] = 0;
		$header['description'] = "ERROR: Error in registration.";
		$respContent->addHeader($header, "result");
		
		$response = array();
		$response['description'] = "ERROR: Error in registration. This may be caused by two reasons: email is invalid or already user, the service is temporarily unavailable. Please try again later.";
		
		// Return response
		return $respContent->getReport($response, "*", FALSE, "result");
	}
	
	// Success registration
	// Add header
	$header = array();
	$header['status'] = 1;
	$header['description'] = "Your Redback account has been created.";
	$header['redirect'] = url::resolve("api", "/account/login.php");
	$respContent->addHeader($header, "result");
	
	$response = array();
	$response['description'] = "Your Redback account has been created.";
	
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
//#section_end#
?>