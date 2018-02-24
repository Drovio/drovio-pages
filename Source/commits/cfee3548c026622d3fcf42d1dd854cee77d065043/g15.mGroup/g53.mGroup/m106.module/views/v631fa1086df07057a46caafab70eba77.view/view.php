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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Developer\profiler\logger;
use \API\Resources\literals\moduleLiteral;
use \UI\Presentation\notification;
use \UI\Interactive\forms\switchButton;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (logger::status() === FALSE)
	{
		logger::activate();
		$messageId = "info.loggerEnabledPrompt";
		$status = TRUE;
	}
	else
	{
		logger::deactivate();
		$messageId = "info.loggerDisabledPrompt";
		$status = FALSE;
	}
	
	// Create notification
	$notification = new notification();
	$message = moduleLiteral::get($moduleID, $messageId);
	$ntf = $notification->build($type = "default", $header = FALSE, $footer = FALSE)->append($message)->get();
	
	return switchButton::getReport($status, $ntf);
}
return FALSE;
//#section_end#
?>