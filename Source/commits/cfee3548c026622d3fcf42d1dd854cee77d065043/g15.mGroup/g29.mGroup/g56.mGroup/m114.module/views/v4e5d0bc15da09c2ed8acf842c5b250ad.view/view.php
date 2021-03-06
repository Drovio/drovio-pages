<?php
//#section#[header]
// Module Declaration
$moduleID = 114;

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
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;

$id = urldecode($_GET['ri']);
$subPath = urldecode($_GET['sp']);
$fileName = urldecode($_GET['fn']);

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");
$page->build("Media Preview", "mediaPreview");

$explorer = new fileExplorer("/Library/Media", $id);
$response = $explorer->previewFile($fileName, $subPath);

$page->appendToSection("mainContent", $response);

// Return output
return $page->getReport();
//#section_end#
?>