<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

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
importer::import("UI", "Forms");
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
$page->build($pageTitle, "devCenterPage");

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


// Info Bullets
$bullets = DOM::create("ul", "", "", "devPrograms");
DOM::append($infoContainer, $bullets);

// Programs
$title = moduleLiteral::get($moduleID, "lbl_programs");
$bulletLi = DOM::create("li");
$bulletA = DOM::create("a");
$bulletUrl = Url::resolve("developer", "/programs/");
DOM::attr($bulletA, "href", $bulletUrl);
DOM::attr($bulletA, "target", "_blank");
DOM::append($bulletA, $title);
DOM::append($bulletLi, $bulletA);
DOM::append($bullets, $bulletLi);

// Documentation
$title = moduleLiteral::get($moduleID, "lbl_documentation");
$bulletLi = DOM::create("li");
$bulletA = DOM::create("a");
$bulletUrl = Url::resolve("developer", "/docs/");
DOM::attr($bulletA, "href", $bulletUrl);
DOM::attr($bulletA, "target", "_blank");
DOM::append($bulletA, $title);
DOM::append($bulletLi, $bulletA);
DOM::append($bullets, $bulletLi);

// Bugs
$title = moduleLiteral::get($moduleID, "lbl_bugReporting");
$bulletLi = DOM::create("li");
$bulletA = DOM::create("a");
$bulletUrl = Url::resolve("www", "/help/");
DOM::attr($bulletA, "href", $bulletUrl);
DOM::attr($bulletA, "target", "_blank");
DOM::append($bulletA, $title);
DOM::append($bulletLi, $bulletA);
DOM::append($bullets, $bulletLi);

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

return $page->getReport();
//#section_end#
?>