<?php
//#section#[header]
// Module Declaration
$moduleID = 167;

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
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \API\Security\account;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;

$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// If user is guest, redirect to login first
if (!account::validate())
{
	$url = url::resolve("login", "/");
	$params = array();
	$params['return_path'] = "enroll.php";
	$params['return_sub'] = "developer";
	$url = url::get($url, $params);
	return $actionFactory->getReportRedirect($url);
}

// Check if user is already member of the DEVELOPER group
if (account::validate() && privileges::accountToGroup("DEVELOPER"))
	return $actionFactory->getReportRedirect("/", "developer");
	
	
// Build Module Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devEnrollPage", TRUE);
	
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Add account to DEVELOPER group
	if (isset($_POST['agree']))
		privileges::addAccountToGroup(account::getAccountID(), "DEVELOPER");
	
	// Redirect
	return $actionFactory->getReportRedirect("/", "developer", TRUE);
}


// Title
$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select("h1.title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_pageDesc");
$header = HTML::select("h4.subtitle")->item(0);
DOM::append($header, $title);

$terms = moduleLiteral::get($moduleID, "lbl_devTerms");
$url = url::resolve("developer", "/docs/terms/");
$wl = $page->getWeblink($url, $terms, "_blank");
$container = HTML::select(".header .content")->item(0);
DOM::append($container, $wl);


$agreeForm = HTML::select(".agreeForm")->item(0);
$form = new simpleForm("enrollForm");
$enrollForm = $form->build($moduleID, "", FALSE)->get();
DOM::append($agreeForm, $enrollForm);

// Agreement checkbox
$title = moduleLiteral::get($moduleID, "lbl_agreeTerms");
$input = $form->getInput($type = "checkbox", $name = "agree", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$label = $form->getLabel($title, $for = DOM::attr($input, "id"), $class = "");
$form->append($input);
$form->append($label);

$title = moduleLiteral::get($moduleID, "lbl_enroll");
$button = $form->getSubmitButton($title, $id = "");
$form->append($button);

return $page->getReport();
//#section_end#
?>