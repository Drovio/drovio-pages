<?php
//#section#[header]
// Module Declaration
$moduleID = 108;

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
use \API\Developer\components\moduleObject;
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get POST Variables
	$id = $_POST['moduleId'];
	$seed = $_POST['auxSeed'];
	$title = $_POST['moduleTitle'];
	$description = $_POST['moduleDescription'];
	$imports = (isset($_POST['imports']) ? $_POST['imports'] : array());
	$inner = (isset($_POST['inner']) ? $_POST['inner'] : array());

	// Create new Module Object
	$moduleObject = new moduleObject($id);
	$module = $moduleObject->getModule("", $seed);
	
	// Update module
	$content = $_POST['wideContent'];
	$status = $module->update($title, $description, $content, $imports, $inner);	
	
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