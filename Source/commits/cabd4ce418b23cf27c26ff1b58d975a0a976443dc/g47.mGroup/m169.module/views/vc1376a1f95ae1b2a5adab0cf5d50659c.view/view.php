<?php
//#section#[header]
// Module Declaration
$moduleID = 169;

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
importer::import("API", "Literals");
importer::import("UI", "Html");
importer::import("AEL", "Platform");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \DEV\Apps\appPlayer;
use \DEV\Apps\appManager;
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

// Load application and check if it is valid and up and running
$app = new application($appID, $appName);
$appInfo = $app->info();
if (is_null($appInfo) || ($appInfo['projectStatus'] != 3 && $appInfo['projectStatus'] != 4))
{
	// Return Error Page
	$page->build("Application Error", "applicationError", TRUE);
	
	// Add notification
	$ntf = DOM::create("h2", "Application Error. The application is unavailable.");
	$appContainer = HTML::select("#applicationContainer")->item(0);
	HTML::append($appContainer, $ntf);
	
	return $page->getReport();
}

// Build the module for a valid application
$appName = $appInfo['title'];
$page->build($appName, "applicationPlayer", TRUE);

// Add action for css and js
$value = appManager::getPublishedAppFolder($appID)."/style.css";
$page->addReportAction("css.application", $value);

$value = appManager::getPublishedAppFolder($appID)."/script.js";
$page->addReportAction("js.application", $value);

// Add action for loading the first view
$page->addReportAction("start.application");

// Return output
return $page->getReport();
//#section_end#
?>