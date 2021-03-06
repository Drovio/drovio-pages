<?php
//#section#[header]
// Module Declaration
$moduleID = 66;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\loginForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Module Page
$page = new HTMLModulePage("freeLayout");
$actionFactory = $page->getActionFactory();

// If user is already logged in, go to my
if (account::validate())
	return $actionFactory->getReportRedirect(url::resolve("my", "/"));

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "loginPage");

// Page Layout
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("main", $globalContainer); 

// Login Container
$loginContainer = DOM::create("div", "", "loginContainer", "innerContainer");
DOM::append($globalContainer, $loginContainer);


// Main Content
$pageHeaderContent = moduleLiteral::get($moduleID, "title");
$pageHeader = DOM::create("h3");
DOM::append($pageHeader, $pageHeaderContent);
DOM::append($loginContainer, $pageHeader);

$form = new loginForm();
$loginURL = url::resolve("www", "/ajax/account/login.php");
$loginForm = $form->build(NULL, $action = $loginURL, $usernameValue = "", $rememberMe = TRUE)->get();
DOM::append($loginContainer, $loginForm);

// Check for specific redirection
if (isset($_GET['return_path']) && isset($_GET['return_sub']))
{
	$input = $form->getInput($type = "hidden", $name = "return_path", $value = $_GET['return_path'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$return_sub = (empty($_GET['return_sub']) ? "www" : $_GET['return_sub']);
	$input = $form->getInput($type = "hidden", $name = "return_sub", $value = $return_sub, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
}


// Check if page has error (from redirect) and create error notification
if (isset($_GET['error']))
{
	$errFormNtf = new formErrorNotification();
	$errorNotification = $errFormNtf->build()->get();
	$form->appendReport($errorNotification);
}

// Forgot password context
$forgotPrompt = moduleLiteral::get($moduleID, "lbl_forgotPasswordPrompt");
$forgotHeader = DOM::create("h4", "", "", "forgot");
DOM::append($forgotHeader, $forgotPrompt);
DOM::append($loginContainer, $forgotHeader);

$forgotContent = moduleLiteral::get($moduleID, "lbl_passwordClick");
$forgotA = DOM::create("a");
$forgotUrl = Url::resolve("login", "/reset.php");
DOM::attr($forgotA, "href", $forgotUrl);
DOM::attr($forgotA, "target", "_blank");
DOM::append($forgotA, $forgotContent);
DOM::append($forgotHeader, $forgotA);



return $page->getReport();
//#section_end#
?>