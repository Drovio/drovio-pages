<?php
//#section#[header]
// Module Declaration
$moduleID = 66;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

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
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// If user is already logged in, go to my
if (account::validate())
	return $actionFactory->getReportRedirect(url::resolve("my", "/"));

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "loginPageContainer", TRUE);

// Get login box
$loginBoxMain = HTML::select(".whiteBox .main")->item(0);

$form = new simpleForm();
$loginURL = url::resolve("www", "/ajax/account/login.php");
$loginForm = $form->build(NULL, $loginURL, FALSE)->get();
DOM::append($loginBoxMain, $loginForm);

$usernameValue = "";
$input = $form->getInput($type = "text", $name = "username", $value = $usernameValue, $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$ph = literal::dictionary("username", FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
$form->append($input);

$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
$ph = literal::dictionary("password", FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
$form->append($input);

$input = $form->getInput($type = "checkbox", $name = "rememberme", $value = "", $class = "lpchk", $autofocus = FALSE, $required = FALSE);
$form->append($input);
$text = literal::get("global::forms::login", "lbl_keepLoggedIn");
$forID = DOM::attr($input, "id");
$label = $form->getLabel($text, $forID, $class = "lplbl");
$form->append($label);

$title = literal::dictionary("login");
$input = $form->getSubmitButton($title);
$form->append($input);

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
//#section_end#
?>