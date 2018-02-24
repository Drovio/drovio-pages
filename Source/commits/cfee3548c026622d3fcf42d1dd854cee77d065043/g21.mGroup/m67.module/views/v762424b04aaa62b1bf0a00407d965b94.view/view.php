<?php
//#section#[header]
// Module Declaration
$moduleID = 67;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \API\Security\account;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formFactory;

// Create Module Page
$page = new HTMLModulePage("freeLayout");
$actionFactory = $page->getActionFactory();

// Check registered user
if (account::validate())
	return $actionFactory->getReportRedirect("/", "my");
	
if ($_SERVER['REQUEST_METHOD'] == "POST")
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
		$err = $errFormNtf->addErrorHeader("lbl_firstname_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_firstname_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Lastname
	if (empty($_POST['lastname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_lastname");
		$err = $errFormNtf->addErrorHeader("lbl_lastname_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_lastname_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Email
	$empty = empty($_POST['email']);
	$valid = inputValidator::checkEmail($_POST['email']);
	if ($empty || !$valid)
	{
		$has_error = TRUE;
		
		// Empty
		
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
		if ($empty)
			$errFormNtf->addErrorDescription($err, "lbl_email_empty", $errFormNtf->getErrorMessage("err.required"));
		if (!$valid)
			$errFormNtf->addErrorDescription($err, "lbl_email_notvalid", $errFormNtf->getErrorMessage("err.invalid"));
	}
	
	// Check Email match
	$match = ($_POST['email'] == $_POST['email2']);
	if (!$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_email");
		$err = $errFormNtf->addErrorHeader("lbl_email_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_email_desc", $errFormNtf->getErrorMessage("err.validate"));
	}
	
	// Check Password
	if (empty($_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global::dictionary", "password");
		$err = $errFormNtf->addErrorHeader("lbl_password_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_password_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Register Person
	$dbc = new interDbConnection();
	$dbq = new dbQuery("1361147110", "profile.person");
	
	// Set attributes
	$attr = array();
	$attr["firstname"] = $_POST['firstname'];
	$attr["lastname"] = $_POST['lastname'];
	$attr["email"] = $_POST['email'];
	$attr["password"] = hash("SHA256", $_POST['password']);
	// Execute
	$result = $dbc->execute($dbq, $attr);
	
	// If there is an error in registration, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "accountRegisterTitle");
		$err = $errFormNtf->addErrorHeader("register_h", $err_header);
		$errSpan = moduleLiteral::get($moduleID, "lbl_registerError");
		$errFormNtf->addErrorDescription($err, "register_desc", $errSpan);
		return $errFormNtf->getReport();
	}
	
	// Successfull Registration, login and go to my
	account::login($attr["email"], $_POST['password']);
	return $actionFactory->getReportRedirect("/", "my", TRUE);
}


// Build Page
$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "registrationPage");

// Get page literals
$literals = moduleLiteral::get($moduleID);

// page container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("main", $globalContainer);

// Left container
$infoContainer = DOM::create("div", "", "", "infoContainer");
DOM::append($globalContainer, $infoContainer);

// Logo
$logoDiv = DOM::create("div", "", "", "logoContainer");
DOM::append($infoContainer, $logoDiv);

// Subtitle
$subContent = $literals["lbl_subTitle"];
$subTitle = DOM::create("h3", "", "", "infoTitle");
DOM::append($subTitle, $subContent);
DOM::append($infoContainer, $subTitle);

// Connect container
$connectContainer = DOM::create("div", "", "", "connectContainer");
DOM::append($globalContainer, $connectContainer);

// boxTitle
$subContent = $literals["accountRegisterTitle"];
$subTitle = DOM::create("h2", "", "", "boxTitle");
DOM::append($subTitle, $subContent);
DOM::append($connectContainer, $subTitle);

// Create registration form
$sForm = new simpleForm("registrationForm");
$registrationForm = $sForm->build($moduleID, "", FALSE)->get();
DOM::append($connectContainer, $registrationForm);

// Registration Form Row Function
function getFormRow($input)
{
	$formRow = DOM::create("div", "", "", "reg formRow");
	DOM::append($formRow, $input);
	return $formRow;
}

$literalsText = moduleLiteral::get($moduleID, "", FALSE);

// Firstname
$fInput = $sForm->getInput($type = "text", $name = "firstname", $value = "", $class = "uiRegInput small f", $autofocus = TRUE, $required = TRUE);
DOM::attr($fInput, "placeholder", $literalsText["lbl_firstname"]);
$formRow = getFormRow($fInput);

// Lastname
$fInput = $sForm->getInput($type = "text", $name = "lastname", $value = "", $class = "uiRegInput small", $autofocus = FALSE, $required = TRUE);
DOM::attr($fInput, "placeholder", $literalsText["lbl_lastname"]);
DOM::append($formRow, $fInput);
$sForm->append($formRow);

// Email
$fInput = $sForm->getInput($type = "text", $name = "email", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
DOM::attr($fInput, "placeholder", $literalsText["lbl_email"]);
$formRow = getFormRow($fInput);
$sForm->append($formRow);

// Re-enter your email
$fInput = $sForm->getInput($type = "text", $name = "email2", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
DOM::attr($fInput, "placeholder", $literalsText["lbl_reenterEmail"]);
$formRow = getFormRow($fInput);
$sForm->append($formRow);

// Password
$fInput = $sForm->getInput($type = "password", $name = "password", $value = "", $class = "uiRegInput", $autofocus = FALSE, $required = TRUE);
DOM::attr($fInput, "placeholder", $literalsText["lbl_password"]);
$formRow = getFormRow($fInput);
$sForm->append($formRow);

// Terms of Service and Privacy Policy Text
$legalContent = $literals['legalText'];
$legal = DOM::create("p", "", "", "lgl");
DOM::append($legal, $legalContent);
$sForm->append($legal);

// Password
$title = $literals['lbl_submit'];
$fInput = $sForm->getSubmitButton($title, $id = "regSubmit");
$formRow = getFormRow($fInput);
$sForm->append($formRow);

return $page->getReport();
//#section_end#
?>