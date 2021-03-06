<?php
//#section#[header]
// Module Declaration
$moduleID = 66;

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
importer::import("API", "Platform");
importer::import("API", "Security");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Platform\engine;
use \API\Security\account;
use \UI\Html\HTMLContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Logout account
	account::logout();
	
	// Restart engine
	engine::restart();
}

// Return Redirect
$content = new HTMLContent();
$actionFactory = $content->getActionFactory();
return $actionFactory->getReportRedirect("/", "www", $formSubmit = TRUE);
//#section_end#
?>