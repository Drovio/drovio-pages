<?php
//#section#[header]
// Module Declaration
$moduleID = 319;

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
importer::import("API", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\popups\popup;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// If user is already logged in, go to my
if (account::validate())
	return $actionFactory->getReportRedirect(url::resolve("my", "/"));

$pageContent->build("", "loginPopupContainer", TRUE);

// Get login box
$loginBoxMain = HTML::select(".loginPopup .main")->item(0);

$form = new simpleForm();
$loginURL = url::resolve("www", "/ajax/account/login.php");
$loginForm = $form->build(NULL, $loginURL, FALSE)->get();
DOM::append($loginBoxMain, $loginForm);

// Set login type to page
$input = $form->getInput($type = "hidden", $name = "logintype", $value = "page", $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add sides into form
$rememberMeContainer = HTML::select(".loginPopup .main .leftSide")->item(0);
$form->append($rememberMeContainer);

$formInputContainer = HTML::select(".loginPopup .main .formInputContainer")->item(0);
$form->append($formInputContainer);

$usernameValue = "";
$input = $form->getInput($type = "text", $name = "username", $value = $usernameValue, $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$ph = literal::dictionary("username", FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
DOM::append($formInputContainer, $input);

$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
$ph = literal::dictionary("password", FALSE);
DOM::attr($input, "placeholder", ucfirst($ph));
DOM::append($formInputContainer, $input);


$rcont = HTML::select("#rsession .ricnt")->item(0);
$input = $form->getInput($type = "radio", $name = "rememberme", $value = "off", $class = "lpchk", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "checked", TRUE);
DOM::append($rcont, $input);
$text = moduleLiteral::get($moduleID, "lbl_noTrust");
$forID = "remember_me_off";
DOM::attr($input, "id", $forID);
$label = $form->getLabel($text, $forID, $class = "lplbl");
DOM::append($rcont, $label);

$rcont = HTML::select("#rtrust .ricnt")->item(0);
$input = $form->getInput($type = "radio", $name = "rememberme", $value = "on", $class = "lpchk", $autofocus = FALSE, $required = FALSE);
DOM::append($rcont, $input);
$text = moduleLiteral::get($moduleID, "lbl_trustComputer");
$forID = "remember_me_on";
DOM::attr($input, "id", $forID);
$label = $form->getLabel($text, $forID, $class = "lplbl");
DOM::append($rcont, $label);


$title = literal::dictionary("login");
$input = $form->getSubmitButton($title);
DOM::append($formInputContainer, $input);


// Create popup
$popup = new popup();
$popup->type(popup::TP_PERSISTENT, FALSE);
$popup->background(TRUE);
$popup->fade(TRUE);
$popup->build($pageContent->get());

// Get popup report
return $popup->getReport();
//#section_end#
?>