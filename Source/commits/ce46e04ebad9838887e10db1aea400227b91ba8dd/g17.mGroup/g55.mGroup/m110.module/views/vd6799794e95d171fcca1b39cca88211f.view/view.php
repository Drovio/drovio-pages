<?php
//#section#[header]
// Module Declaration
$moduleID = 110;

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
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\resources\layouts\systemLayout;
use \API\Developer\resources\layouts\ebuilderLayout;
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;

$group = $_POST['group'];
$layoutName = $_POST['name'];

switch($group)
{
	case 'ebuilder' :
		$layoutManager = new ebuilderLayout($layoutName);
		$stripWrapper = TRUE;
		break;
	case 'system' :
		$layoutManager = new systemLayout($layoutName);
		$stripWrapper = FALSE;
		break;
	default :
		break;	
}

// Get Source Code
$cssCode = $_POST['objectCSS'];
$xmlCode = $_POST['objectXML'];

// Update Source Code
$statusModel = $layoutManager->saveModel($cssCode);
// Update Source Code
$statusStructure = $layoutManager->saveStructure($xmlCode, $stripWrapper);

// Build Notification
$reportNtf = new notification();
if (!($statusModel || $statusStructure))
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

$reportNtf->append($reportMessage);
$notification = $reportNtf->get();

return redWIDE::getNotificationResult($notification, ($status === TRUE));
//#section_end#
?>