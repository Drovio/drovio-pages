<?php
//#section#[header]
// Module Declaration
$moduleID = 112;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Resources\DOMParser;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;

use \INU\Views\fileExplorer;

$page = new HTMLModulePage("simpleFullScreen");
$page->build(moduleLiteral::get($moduleID, "lbl_mediaManager", FALSE), "mediaPage");

$explorer = new fileExplorer("/Library/Media/", "fex_libraryMedia");

$container = $explorer->build()->get();

$page->appendToSection("mainContent", $container);

return $page->getReport();
//#section_end#
?>