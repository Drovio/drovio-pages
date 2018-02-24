<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$pageContent->build("", "navigationContent", TRUE);

if (!account::validate())
{
	// Create login form
	$login_formContainer = HTML::select(".loginDialogContainer .login_formContainer")->item(0);
	$form = new simpleForm();
	$loginURL = url::resolve("www", "/ajax/account/login.php");
	$loginForm = $form->build(NULL, $loginURL)->get();
	DOM::append($login_formContainer, $loginForm);
	
	$input = $form->getInput($type = "hidden", $name = "logintype", $value = "page", $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$title = literal::dictionary("username");
	$input = $form->getInput($type = "text", $name = "username", $value = $usernameValue, $class = "", $autofocus = TRUE, $required = TRUE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	$title = literal::dictionary("password");
	$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
	
	$title = moduleLiteral::get($moduleID, "lbl_rememberme");
	$input = $form->getInput($type = "checkbox", $name = "rememberme", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	$form->insertRow($title, $input, $required = FALSE, $notes = "");
}
else
{
	$loginLI = HTML::select("li.login")->item(0);
	HTML::replace($loginLI, NULL);
	
	$loginContainer = HTML::select(".loginDialogContainer")->item(0);
	HTML::replace($loginContainer, NULL);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>