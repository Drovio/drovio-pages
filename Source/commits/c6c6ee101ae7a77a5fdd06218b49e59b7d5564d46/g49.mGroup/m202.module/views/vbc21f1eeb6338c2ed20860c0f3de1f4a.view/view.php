<?php
//#section#[header]
// Module Declaration
$moduleID = 202;

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
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Redirect to developer tools api page
$url = url::resolve("developer", "/tools/api/");

header("Location: ".$url);
return $actionFactory->getReportRedirect($url);
//#section_end#
?>