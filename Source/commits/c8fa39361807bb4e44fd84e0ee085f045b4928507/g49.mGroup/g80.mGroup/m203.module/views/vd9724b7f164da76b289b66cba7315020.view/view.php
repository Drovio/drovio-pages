<?php
//#section#[header]
// Module Declaration
$moduleID = 203;

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
use \UI\Html\HTMLModulePage;

$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build Module Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devTools", TRUE);


$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select(".head .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_pageSub");
$header = HTML::select(".head .sub")->item(0);
DOM::append($header, $title);

return $page->getReport();
//#section_end#
?>