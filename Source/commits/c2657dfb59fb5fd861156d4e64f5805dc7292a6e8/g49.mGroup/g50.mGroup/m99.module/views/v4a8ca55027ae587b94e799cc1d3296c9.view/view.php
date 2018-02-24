<?php
//#section#[header]
// Module Declaration
$moduleID = 99;

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
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;
use \UI\Html\pageComponents\htmlComponents\weblink;
use \INU\Developer\documentor;
use \INU\Developer\documentationViewer;

$content = new HTMLContent();
$container = $content->build("documentViewerContainer")->get();

// Load Documentation
$doc = new documentationViewer($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid'], $_GET['domain']);
$documentation = $doc->build()->get();
DOM::append($container, $documentation);

// Return the report
return $content->getReport("#docViewer");
//#section_end#
?>