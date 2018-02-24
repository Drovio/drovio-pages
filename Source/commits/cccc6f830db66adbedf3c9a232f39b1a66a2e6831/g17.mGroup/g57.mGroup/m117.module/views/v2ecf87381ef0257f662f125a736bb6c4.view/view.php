<?php
//#section#[header]
// Module Declaration
$moduleID = 117;

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
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;

$themeName = $_POST['theme'];
$templateID = $_POST['templateId'];

// Get Source Code
$cssCode = $_POST['objectCSS'];

$templateObject = new template();
	
// Try to load Object
$templateManager = new templateManager();
$templateObject = $templateManager->getTemplate($templateID);

// Build Notification
$reportNtf = new notification();
if(is_null($templateObject))
{
	$message = "err.save_error";
	$reportNtf->build($type = "warning", $header = TRUE, $footer = FALSE);
	$reportMessage = DOM::create("span", "Could not Load Template. Please Refresh Psge and Retry");
}
else
{	
	//Try to create new layout
	$success = $templateObject->saveTheme($themeName, $cssCode);
	if (!$success )
	{	
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
		$reportPopup->timeout(FALSE);
		
		
	}
	else
	{
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
}

$reportNtf->append($reportMessage);
$notification = $reportNtf->get();

return redWIDE::getNotificationResult($notification, ($status === TRUE));
//#section_end#
?>