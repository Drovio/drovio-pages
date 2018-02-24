<?php
//#section#[header]
// Module Declaration
$moduleID = 178;

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
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\documentParser;
use \UI\Html\HTMLModulePage;

$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("OneColumnCentered");
$page->build($pageTitle, "appsGuide");

$documentParser = new documentParser();
$document = $documentParser->load("appCenter::guide.html", $text = FALSE);

$docElem = DOM::create('div', DOM::import($document));
$page->appendToSection("mainContent", $docElem);

return $page->getReport();
//#section_end#
?>