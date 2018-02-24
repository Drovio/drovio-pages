<?php
//#section#[header]
// Module Declaration
$moduleID = 194;

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

// Create Module Page
$page = new HTMLModulePage();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "discoverPage", TRUE);


// Page title
$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select("h1.title")->item(0);
DOM::append($header, $title);

// Return output
return $page->getReport();
//#section_end#
?>