<?php
//#section#[header]
// Module Declaration
$moduleID = 204;

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
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devAPIPage", TRUE);

$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select(".head .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_pageSub");
$header = HTML::select(".head .sub")->item(0);
DOM::append($header, $title);


// Api Reference
$title = moduleLiteral::get($moduleID, "lbl_apiref_title");
$header = HTML::select(".apiref .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_apiref_sub");
$header = HTML::select(".apiref .sub")->item(0);
DOM::append($header, $title);


// Knowledge
$title = moduleLiteral::get($moduleID, "lbl_know_title");
$header = HTML::select(".knowledge .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_know_sub");
$header = HTML::select(".knowledge .sub")->item(0);
DOM::append($header, $title);


// Open Code
$title = moduleLiteral::get($moduleID, "lbl_oc_title");
$header = HTML::select(".opencode .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_oc_sub");
$header = HTML::select(".opencode .sub")->item(0);
DOM::append($header, $title);

// Return output
return $page->getReport();
//#section_end#
?>