<?php
//#section#[header]
// Module Declaration
$moduleID = 137;

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
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Get application id
$appID = $_GET['id'];
$appName = $_GET['name'];

if (empty($appID) && empty($appName))
{
	// Application id doesn't exist, return to home page
	return $actionFactory->getReportRedirect("/", "apps");
}

// Build the module for a valid application
$page->build("", "applicationTester", TRUE);


// Test the application
appTester::init();

// Build the module for a valid application
$page->build("", "applicationTester", TRUE);




// Add action for loading the first view
$page->addReportAction("start.application");

// Return output
return $page->getReport();
//#section_end#
?>