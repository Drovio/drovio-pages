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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\privileges;
use \API\Security\account;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Add account to DEVELOPER group
	privileges::addAccountToGroup(account::getAccountID(), "DEVELOPER");
}

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "devPrograms");

// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageHeader");
$header = DOM::create("h1", $title);
$page->appendToSection("mainContent", $header);


$form = new simpleForm("devPrograms");
$pForm = $form->build($moduleID, "", FALSE)->get();
$page->appendToSection("mainContent", $pForm);

// Programs
$pContainer = DOM::create("div", "", "", "programContainer");
$form->append($pContainer);

// Developer Group
$appDeveloper = privileges::accountToGroup("DEVELOPER");
$title = DOM::create("label", moduleLiteral::get($moduleID, "lbl_rbDeveloper"));
$program = DOM::create("a", $title, "", "program".($appDeveloper ? " active" : ""));
DOM::append($pContainer, $program);
if (!$appDeveloper)
{
	DOM::attr($title, "class", "pl");
	DOM::attr($title, "for", "ap");
	$checkbox = $form->getInput($type = "checkbox", $name = "group", $value = "ad", $class = "pCheck", $autofocus = FALSE, $required = FALSE);
	DOM::attr($checkbox, "id", "ap");
	DOM::append($program, $checkbox);
}
else
{
	$url = url::resolve("developer", "/dashboard.php");
	DOM::attr($program, "href", $url);
	DOM::attr($program, "target", "_blank");
}

return $page->getReport();
//#section_end#
?>