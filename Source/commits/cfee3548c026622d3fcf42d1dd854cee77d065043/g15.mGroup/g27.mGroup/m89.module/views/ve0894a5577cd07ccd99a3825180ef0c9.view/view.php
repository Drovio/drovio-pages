<?php
//#section#[header]
// Module Declaration
$moduleID = 89;

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
importer::import("UI", "Notifications");
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\sdk\sdkObject;
use \API\Developer\resources\documentation\classDocComments;
use \API\Developer\resources\documentation\classDocumentor;
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;

// Initialize SDK Object
$sdkObj = new sdkObject($_POST['libID'], $_POST['pkgID'], $_POST['nsID'], $_POST['objID']);

// Get Source Code
$sourceCode = $_POST['wideContent'];

// Get Documentation
$manual = $_POST['classXMLModel'];

// Update Documentation + Pretify Source Code (with comments)
if (classDocumentor::isValidDocumentation($manual))
{
	$sdkObj->updateSourceDoc($manual);
	$updated_manual = $sdkObj->getSourceDoc();
	$strippedCode = classDocComments::stripSourceCode($sourceCode);
	$sourceCode = classDocComments::pretifySourceCode($strippedCode, $updated_manual, $sdkObj->getLibrary(), $sdkObj->getPackage(), $sdkObj->getNamespace());
}

// Update Source Code
$status = $sdkObj->updateSourceCode($sourceCode);

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
//#section_end#
?>