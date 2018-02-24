<?php
//#section#[header]
// Module Declaration
$moduleID = 170;

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
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;
use \UI\Html\pageComponents\htmlComponents\weblink;
use \INU\Developer\documentationViewer;

$content = new HTMLContent();
$container = $content->build("documentViewerContainer")->get();

echo $_GET['lib']."\\".$_GET['pkg']."\\".$_GET['ns']."\\".$_GET['oid'];


// Load Documentation
$doc = new documentationViewer($_GET['lib'], $_GET['pkg'], $_GET['ns'], $_GET['oid'], "SDK");
$documentation = $doc->build()->get();
DOM::append($container, $documentation);

// Return the report
return $content->getReport();
//#section_end#
?>