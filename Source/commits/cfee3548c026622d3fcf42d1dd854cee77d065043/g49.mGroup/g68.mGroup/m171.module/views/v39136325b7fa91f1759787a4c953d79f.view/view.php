<?php
//#section#[header]
// Module Declaration
$moduleID = 171;

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
importer::import("API", "Developer");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\appManager;
use \API\Developer\appcenter\application;
use \API\Developer\misc\vcs;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;

// Create Module Page
$pageContent = new HTMLContent();

// Get application id
$appID = $_GET['appID'];

if (empty($appID))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}

// Validate and Load application info
$application = appManager::getApplicationData($appID);
if (is_null($application))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}

// Init application
$devApp = new application($appID);
$vcs = $devApp->getVCS();

// Build the module
$pageContent->build("", "vcsSectionContent");

$title = DOM::create("p", "This BETA version doesn't support branch viewer yet.");
$pageContent->append($title);

// Return output
return $pageContent->getReport();
//#section_end#
?>