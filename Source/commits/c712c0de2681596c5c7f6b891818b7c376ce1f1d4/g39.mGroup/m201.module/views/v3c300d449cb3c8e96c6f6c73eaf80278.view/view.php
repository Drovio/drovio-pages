<?php
//#section#[header]
// Module Declaration
$moduleID = 201;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "redbackCareers", TRUE);

$context = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select(".welcome h1")->item(0);
HTML::append($header, $context);

$context = moduleLiteral::get($moduleID, "lbl_pageSub");
$header = HTML::select(".welcome h2")->item(0);
HTML::append($header, $context);


// Opening Jobs
$jobSection = HTML::select(".jobs")->item(0);

$context = moduleLiteral::get($moduleID, "lbl_jobsHeader");
$header = HTML::select(".jobs h3")->item(0);
HTML::append($header, $context);

$context = moduleLiteral::get($moduleID, "lbl_jobsDesc");
$desc = DOM::create("p", $context);
DOM::append($jobSection, $desc);

// Internship Program
$internSection = HTML::select(".internship")->item(0);

$context = moduleLiteral::get($moduleID, "lbl_internshipHeader");
$header = HTML::select(".internship h3")->item(0);
HTML::append($header, $context);

$context = moduleLiteral::get($moduleID, "lbl_internshipDesc");
$desc = DOM::create("p", $context);
DOM::append($internSection, $desc);

$context = moduleLiteral::get($moduleID, "lbl_internshipDirections");
$directions = DOM::create("p", $context);
DOM::append($internSection, $directions);

// Apply form
$form = new simpleForm();
$intersnhipForm = $form->build($moduleID, "internApplication", FALSE)->get();
DOM::append($internSection, $intersnhipForm);


// Full name
$title = moduleLiteral::get($moduleID, "lbl_applicantFullname");
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Email
$title = moduleLiteral::get($moduleID, "lbl_applicantEmail");
$input = $form->getInput($type = "email", $name = "email", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Phone
$title = moduleLiteral::get($moduleID, "lbl_applicantPhone");
$input = $form->getInput($type = "text", $name = "phone", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Address
$title = moduleLiteral::get($moduleID, "lbl_applicantAddress");
$input = $form->getInput($type = "text", $name = "address", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// City
$title = moduleLiteral::get($moduleID, "lbl_applicantCity");
$input = $form->getInput($type = "text", $name = "city", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// Country
$title = moduleLiteral::get($moduleID, "lbl_applicantCountry");
$input = $form->getInput($type = "text", $name = "country", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");


// More about you
$title = moduleLiteral::get($moduleID, "lbl_applicantTellusmore");
$input = $form->getTextarea($name = "details", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");

$title = literal::dictionary("apply");
$submitButton = $form->getSubmitButton($title, "applyBtn");
$form->append($submitButton);


// Return output
return $page->getReport();
//#section_end#
?>