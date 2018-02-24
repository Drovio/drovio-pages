<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

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
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \UI\Presentation\popups\popup;
use \INU\Developer\vcsControl;

// Initialize Application
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$htmlContent->append($errorMessage);
	return $htmlContent->getReport();
}

// Create vcs commit manager
$appMan = new appManager();
$repository = $appMan->getDevAppFolder($appID);
$vcs = new vcsControl("appVCS", $repository, TRUE);
$vcsControl = $vcs->build()->get();


// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "obedient", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");

$vcsPopup->build($vcsControl);
return $vcsPopup->getReport();
//#section_end#
?>