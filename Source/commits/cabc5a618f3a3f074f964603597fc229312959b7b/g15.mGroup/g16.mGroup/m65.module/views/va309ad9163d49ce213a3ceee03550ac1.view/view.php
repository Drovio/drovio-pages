<?php
//#section#[header]
// Module Declaration
$moduleID = 65;

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
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \UI\Presentation\popups\popup;
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;
use \DEV\Modules\module;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Initialize view
	$viewModuleID = $_POST['viewModuleID'];
	$viewID = $_POST['viewID'];
	
	$moduleObject = new module($viewModuleID);
	$viewObject = $moduleObject->getView("", $viewID);
	
	// Check view name update
	$views = $moduleObject->getViews();
	$viewName = $views[$viewID];
	if (!empty($_POST['viewName']) && $_POST['viewName'] != $viewName)
		$moduleObject->updateViewName($viewID, $_POST['viewName']);
	
	// Update info and source
	$status = $viewObject->updateInfo($_POST['dependencies'], $_POST['inner']);
	$status = $viewObject->updatePHPCode($_POST['viewSource']);
	
	// Build popup
	$reportPopup = new popup();
	$reportPopup->timeout(TRUE);
	$reportPopup->fade(TRUE);
	$reportPopup->position('top');
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// TEMP
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
	else if ($status === FALSE)
	{
		// TEMP
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
		$reportPopup->timeout(FALSE);
	}
	else
	{
		$message = "err.save_error";
		$reportNtf->build($type = "warning", $header = TRUE, $footer = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return redWIDE::getNotificationResult($notification, ($status === TRUE));
}
//#section_end#
?>