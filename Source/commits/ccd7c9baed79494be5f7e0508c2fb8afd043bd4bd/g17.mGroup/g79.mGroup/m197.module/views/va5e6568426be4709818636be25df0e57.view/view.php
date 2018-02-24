<?php
//#section#[header]
// Module Declaration
$moduleID = 197;

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
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \UI\Forms\formFactory;
use \UI\Presentation\frames\dialogFrame;

// Create Module Page
$page = new HTMLModulePage("simpleOneColumnCenter");

// Build the module
$page->build("Under Construction", "uc");

// Build Page Content
$globalContainer = DOM::create("div", "", "");
DOM::attr($globalContainer, "style", "height:100%;");
$page->appendToSection("mainContent", $globalContainer);

$ff = new formFactory(); 
$button = $ff->getButton("newWebsite", "new", "");
dialogFrame::setAction($button, $moduleID, 'create');
DOM::append($globalContainer, $button);


// Return output
return $page->getReport();
//#section_end#
?>