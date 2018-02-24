<?php
//#section#[header]
// Module Declaration
$moduleID = 109;

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
importer::import("UI", "Presentation");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \UI\Presentation\frames\dialogFrame;
use \INU\Views\fileExplorer;

// Build the frame
$frame = new dialogFrame();
$frame->build("File Explorer");

// Create file explorer
$explorer = new fileExplorer("/Library/Media/", "fex2_libraryMedia");
$container = $explorer->build()->get();

$frame->append($container);

// Return the report
return $frame->getFrame();
//#section_end#
?>