<?php
//#section#[header]
// Module Declaration
$moduleID = 195;

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
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "internationalPage", TRUE);


$title = moduleLiteral::get($moduleID, "lbl_logoTitle");
$header = HTML::select(".head .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_expandServices");
$header = HTML::select(".head .desc")->item(0);
DOM::append($header, $title);


$title = moduleLiteral::get($moduleID, "lbl_translatorTitle");
$header = HTML::select(".translator .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_translatorDesc");
$header = HTML::select(".translator .desc")->item(0);
DOM::append($header, $title);


$title = moduleLiteral::get($moduleID, "lbl_translatorLink");
$url = url::resolve("developer", "/tools/translator.php");
$wl = $page->getWeblink($url, $title, "_self");
$header = HTML::select(".translator .link")->item(0);
DOM::append($header, $wl);

// Return output
return $page->getReport();
//#section_end#
?>