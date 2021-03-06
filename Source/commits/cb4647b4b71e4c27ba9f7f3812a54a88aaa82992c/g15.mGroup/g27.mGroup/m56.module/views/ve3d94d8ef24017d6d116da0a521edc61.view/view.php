<?php
//#section#[header]
// Module Declaration
$moduleID = 56;

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
importer::import("API", "Profile");
importer::import("API", "Developer");
importer::import("API", "Profile");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\resources\paths;
use \API\Profile\tester;
use \UI\Presentation\popups\popup;
use \INU\Developer\vcsControl;

// Create Module Page
$repository = paths::getDevPath()."/Repository/Core/";
$vcs = new vcsControl("sdkCommitManager", $repository);
$vcsControl = $vcs->build()->get();


// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "obedient", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");

return $vcsPopup->build($vcsControl)->getReport();
//#section_end#
?>