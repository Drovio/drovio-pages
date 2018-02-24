<?php
//#section#[header]
// Module Declaration
$moduleID = 70;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPage'] = 66;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("freeLayout");
$page->build($pageTitle, "frontendPage");

// Get if user is loggedIn
$loggedIn = account::validate();

if (!$loggedIn)
{
	// Registration bar
	$regBar = DOM::create("div", "", "", "registrationBar");
	$page->appendToSection("main", $regBar);
	
	$regContainer = DOM::create("div", "", "", "regContainer");
	DOM::append($regBar, $regContainer);
	
	$regTitleContent = literal::get("global.temp", "lbl_registrationBarTitle");
	$regTitle = DOM::create("h4");
	DOM::append($regTitle, $regTitleContent);
	DOM::append($regContainer, $regTitle);
	
	// Reg Button
	$regButtonTitle = literal::get("global.temp", "lbl_registrationBarButton");
	$regButton = DOM::create("a", "", "", "regBtn");
	$regURL = Url::resolve("my", "/register/");
	DOM::attr($regButton, "href", $regURL);
	DOM::attr($regButton, "target", "_blank");
	DOM::append($regButton, $regButtonTitle);
	DOM::append($regContainer, $regButton);
}

// Global page container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("main", $globalContainer);

// Left container
$infoContainer = DOM::create("div", "", "", "infoContainer".($loggedIn ? " cntr" : ""));
DOM::append($globalContainer, $infoContainer);

// Logo
$logoDiv = DOM::create("div", "", "", "logoContainer");
DOM::append($infoContainer, $logoDiv);

// Subtitle
$subContent = moduleLiteral::get($moduleID, "msg_headMessage");
$subTitle = DOM::create("h1", "", "", "infoTitle");
DOM::append($subTitle, $subContent);
DOM::append($infoContainer, $subTitle);


if (!$loggedIn)
{
	// Connect container
	$connectContainer = DOM::create("div", "", "", "connectContainer");
	DOM::append($globalContainer, $connectContainer);
	
	// boxTitle
	$subContent = moduleLiteral::get($moduleID, "accountLoginTitle");
	$subTitle = DOM::create("h3", "", "", "boxTitle");
	DOM::append($subTitle, $subContent);
	DOM::append($connectContainer, $subTitle);
	
	$lForm = new loginForm();
	$loginForm = $lForm->build($innerModules['loginPage'])->get();
	$sub = Url::getSubDomain();
	$lInput = $lForm->getInput($type = "hidden", $name = "sub", $value = $sub, $class = "", $autofocus = FALSE, $required = FALSE);
	$lForm->append($lInput);
	$host = $_SERVER['HTTP_HOST'];
	$origin = $_SERVER['HTTP_REFERER'];
	$origin = str_replace("http://".$host, "", $origin);
	$lInput = $lForm->getInput($type = "hidden", $name = "origin", $value = $origin, $class = "", $autofocus = FALSE, $required = FALSE);
	$lForm->append($lInput);
	DOM::append($connectContainer, $loginForm);
	
	// Forgot Password
	$forgotContent = moduleLiteral::get($moduleID, "lbl_forgotPassword");
	$forgotA = DOM::create("a", "", "", "forgotP");
	$forgotUrl = Url::resolve("login", "/reset.php");
	DOM::attr($forgotA, "href", $forgotUrl);
	DOM::attr($forgotA, "target", "_blank");
	DOM::append($forgotA, $forgotContent);
	DOM::append($connectContainer, $forgotA);
}

// Create service container
$pageContainer = DOM::create("div", "", "", "pageContainer");
DOM::append($globalContainer, $pageContainer);


// Create service container
$servicesContainer = DOM::create("div", "", "", "servicesContainer");
DOM::append($pageContainer, $servicesContainer);

// Application title
$content = moduleLiteral::get($moduleID, "lbl_rbServices");
$title = DOM::create("h2", $content);
DOM::append($servicesContainer, $title);

// Redback Developer
$title = moduleLiteral::get($moduleID, "lbl_rbDeveloper");
$webC = DOM::create("a", $title);
$url = url::resolve("developer", "/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($servicesContainer, $header);

// Application Center
$title = moduleLiteral::get($moduleID, "lbl_appCenter");
$webC = DOM::create("a", $title);
$url = url::resolve("apps", "/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($servicesContainer, $header);

// eBuilder
$title = moduleLiteral::get($moduleID, "lbl_eBuilder");
$webC = DOM::create("a", $title);
$url = url::resolve("ebuilder", "/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($servicesContainer, $header);

// Support Container
$supportContainer = DOM::create("div", "", "", "supportContainer");
DOM::append($globalContainer, $supportContainer);

// Support Title
$content = moduleLiteral::get($moduleID, "lbl_rbSupport");
$title = DOM::create("h2", $content);
DOM::append($supportContainer, $title);

// Redback support
$title = moduleLiteral::get($moduleID, "lbl_helpCenter");
$webC = DOM::create("a", $title);
$url = url::resolve("support", "/help/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($supportContainer, $header);

return $page->getReport();
//#section_end#
?>