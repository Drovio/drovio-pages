<?php
//#section#[header]
// Module Declaration
$moduleID = 94;

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
use \API\Resources\literals\literal;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "frontendPage", TRUE);

$text = moduleLiteral::get($moduleID, "lbl_pageTitle");
$textContainer = HTML::select("h2.logTitle")->item(0);
DOM::append($textContainer, $text);


$text = moduleLiteral::get($moduleID, "lbl_subtitle");
$textContainer = HTML::select("h3.subTitle")->item(0);
DOM::append($textContainer, $text);

return $page->getReport();
//#section_end#
?>