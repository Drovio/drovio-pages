<?php
//#section#[header]
// Module Declaration
$moduleID = 106;

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
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Forms");
importer::import("UI", "Forms");
importer::import("UI", "Forms");
importer::import("UI", "Forms");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \API\Developer\profiler\sdkTester;
use \UI\Forms\formReport\formNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$status = FALSE;
	
	// Get activated packages
	$packages = $_POST['pkg'];
	
	// Deactivate all packages
	sdkTester::deactivate();
	
	// Activate Packages
	if (is_array($packages))
		foreach ($packages as $pkg => $value)
			$status = sdkTester::activate($pkg);
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
//#section_end#
?>