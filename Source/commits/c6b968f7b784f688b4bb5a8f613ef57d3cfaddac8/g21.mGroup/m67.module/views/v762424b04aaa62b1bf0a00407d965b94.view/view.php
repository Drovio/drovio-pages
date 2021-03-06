<?php
//#section#[header]
// Module Declaration
$moduleID = 67;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Geoloc\locale;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \API\Profile\account;
use \UI\Modules\MPage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get return url
$return_url = engine::getVar('return_url');

// Check registered user
if (account::validate())
{
	// Check for return url or redirect to my
	if (!empty($return_url))
		return $actionFactory->getReportRedirect($return_url);
	
	// Redirect to my
	return $actionFactory->getReportRedirect("/", "my");
}
	
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Firstname
	if (empty($_POST['firstname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_firstname");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Lastname
	if (empty($_POST['lastname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_lastname");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Email
	$empty = empty($_POST['email']);
	$valid = inputValidator::checkEmail($_POST['email']);
	if ($empty || !$valid)
	{
		$has_error = TRUE;
		
		// Empty
		
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addHeader($err_header);
		if ($empty)
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
		if (!$valid)
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
	}
	
	// Check Email match
	$match = ($_POST['email'] == $_POST['email2']);
	if (!$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.validate"));
	}
	
	// Check Password
	if (empty($_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global::dictionary", "password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	$dbc = new dbConnection();
	
	// Check if there is a non-activated account with the provided email.
	$dbq = module::getQuery($moduleID, "get_nonactivated_person");
	
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
		
		$dbq = module::getQuery($moduleID, "register_update");
		$result = $dbc->execute($dbq, $attr);
	}
	else
	{
		// Register New Person
		$dbq = module::getQuery($moduleID, "register");
		$result = $dbc->execute($dbq, $attr);
	}
	
	// If there is an error in registration, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "accountRegisterTitle");
		$err = $errFormNtf->addHeader($err_header);
		$errSpan = moduleLiteral::get($moduleID, "lbl_registerError");
		$errFormNtf->addDescription($err, $errSpan);
		return $errFormNtf->getReport();
	}
	
	// Successful Registration, login account
	account::login($attr["email"], $_POST['password']);
	
	// Check for return url or redirect to my
	if (!empty($return_url))
		return $actionFactory->getReportRedirect($return_url);
	
	return $actionFactory->getReportRedirect("/", "my", TRUE);
}


// Build Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "registerPageContainer", TRUE);

$formContainer = HTML::select(".formContainer")->item(0);

// Create registration form
$form = new simpleForm("registrationForm");
$registrationForm = $form->build($moduleID, "", FALSE)->get();
DOM::append($formContainer, $registrationForm);

// Return url
$input = $form->getInput($type = "hidden", $name = "return_url", $value = $return_url, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Firstname
$fInput = $form->getInput($type = "text", $name = "firstname", $value = "", $class = "uiRegInput small f", $autofocus = TRUE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_firstname", array(), FALSE);
DOM::attr($fInput, "placeholder", $ph);
$formRow = getFormRow($fInput);

// Lastname
$fInput = $form->getInput($type = "text", $name = "lastname", $value = "", $class = "uiRegInput small", $autofocus = FALSE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_lastname", array(), FALSE);
DOM::attr($fInput, "placeholder", $ph);
DOM::append($formRow, $fInput);
$form->append($formRow);


// Email
$fInput = $form->getInput($type = "text", $name = "email", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_email", array(), FALSE);
DOM::attr($fInput, "placeholder", $ph);
$formRow = getFormRow($fInput);
$form->append($formRow);

// Re-enter your email
$fInput = $form->getInput($type = "text", $name = "email2", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_reenterEmail", array(), FALSE);
DOM::attr($fInput, "placeholder", $ph);
$formRow = getFormRow($fInput);
$form->append($formRow); 

// Password
$fInput = $form->getInput($type = "password", $name = "password", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
$ph = moduleLiteral::get($moduleID, "lbl_password", array(), FALSE);
DOM::attr($fInput, "placeholder", $ph);
$formRow = getFormRow($fInput);
$form->append($formRow);

// Submit Button
$title = moduleLiteral::get($moduleID, "lbl_submit", array(), FALSE);
$fInput = $form->getSubmitButton($title, $id = "regSubmit");
$formRow = getFormRow($fInput);
$form->append($formRow);


// Footer year
$trade = HTML::select(".pgFooter .left")->item(0);
$y = DOM::create("span", "".date('Y'));
DOM::append($trade, $y);

// Footer locale
$a_locale = HTML::select("a.locale")->item(0);
$localeInfo = locale::info();
$content = DOM::create("span", $localeInfo['friendlyName']);
DOM::append($a_locale, $content);

return $page->getReport();


// Registration Form Row Function
function getFormRow($input)
{
	$formRow = DOM::create("div", "", "", "reg formRow");
	DOM::append($formRow, $input);
	return $formRow;
}
//#section_end#
?>