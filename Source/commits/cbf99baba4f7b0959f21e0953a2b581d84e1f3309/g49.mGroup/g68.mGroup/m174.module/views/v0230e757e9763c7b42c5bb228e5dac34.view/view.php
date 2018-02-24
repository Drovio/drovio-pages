<?php
//#section#[header]
// Module Declaration
$moduleID = 174;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("UI", "Presentation");
importer::import("INU", "Developer");
importer::import("DEV", "Apps");
importer::import("DEV", "Documentation");
//#section_end#
//#section#[code]
use \UI\Presentation\notification;
use \INU\Developer\redWIDE;
use \DEV\Apps\components\source\sourceObject;
use \DEV\Documentation\classComments;
use \DEV\Documentation\classDocumentor;

// Initialize source object
$sdkObj = new sourceObject($_POST['appID'], $_POST['lib'], $_POST['pkg'], $_POST['ns'], $_POST['oid']);

// Get Source Code
$sourceCode = $_POST['wideContent'];

// Get Documentation
$manual = $_POST['classXMLModel'];

// Update Documentation + Pretify Source Code (with comments)
if (classDocumentor::isValidDocumentation($manual))
{
	$sdkObj->updateSourceDoc($manual);
	$updated_manual = $sdkObj->getSourceDoc();
	$strippedCode = classComments::stripSourceCode($sourceCode);
	$sourceCode = classComments::pretifySourceCode($strippedCode, $updated_manual, $sdkObj->getLibrary(), $sdkObj->getPackage(), $sdkObj->getNamespace());
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