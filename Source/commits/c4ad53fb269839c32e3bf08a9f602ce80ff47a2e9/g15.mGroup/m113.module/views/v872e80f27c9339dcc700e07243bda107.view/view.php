<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;


// Build the page content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "", TRUE);



// Create steps to publish
$syncDbSchema = HTML::select(".syncDbSchema")->item(0);
// Step 1. Check DB Schema
$title = moduleLiteral::get($moduleID, "lbl_checkDbSchema");
$header = HTML::select(".syncDbSchema .header")->item(0);
HTML::append($header, $title);

$form = new simpleForm();
$schemaForm = $form->build($moduleID, "checkDbSchema")->get();
HTML::append($syncDbSchema, $schemaForm);

$title = moduleLiteral::get($moduleID, "lbl_authenticate");
$p = DOM::create("span", $title);
$form->append($p);

// Header
$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");



// Create steps to publish
$syncDbData = HTML::select(".syncDbData")->item(0);
// Step 2. Sync DB Data
$title = moduleLiteral::get($moduleID, "lbl_syncDbData");
$header = HTML::select(".syncDbData .header")->item(0);
HTML::append($header, $title);

$form = new simpleForm();
$schemaForm = $form->build($moduleID, "syncDbData")->get();
HTML::append($syncDbData, $schemaForm);


$title = moduleLiteral::get($moduleID, "lbl_selectCategories");
$p = DOM::create("p", $title);
$form->append($p);

// Get sync tables and checkbox them
$sync = array();
$sync["geoloc"] = "Geolocation Data";
$sync["modules"] = "Modules";
$sync["pages"] = "Pages";
$sync["literals"] = "Literals";
$sync["security"] = "Security Groups and Privileges";
$sync["projects"] = "Projects";
$sync["persons"] = "Persons, Teams and Accounts";

foreach ($sync as $key => $title)
{
	$input = $form->getInput($type = "checkbox", $name = "sync[".$key."]", $value = "", $class = "", $autofocus = FALSE);
	$form->insertRow($title, $input, $required = TRUE, $notes = "");
}


$title = moduleLiteral::get($moduleID, "lbl_authenticate");
$p = DOM::create("span", $title);
$form->append($p);

// Header
$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Return content report
return $pageContent->getReport();
//#section_end#
?>