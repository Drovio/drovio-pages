<?php
//#section#[header]
// Module Declaration
$moduleID = 124;

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
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Developer\resources\paths;
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;

// Media Code
$page = new HTMLModulePage("OneColumnFullscreen");
$page->build("Developer Resource Manager", "devResources");

$explorer = new fileExplorer(paths::getDevRsrcPath(), "dev_rsrc", "Resources");
$container = $explorer->build()->get();

$page->appendToSection("mainContent", $container);
return $page->getReport();
//#section_end#
?>