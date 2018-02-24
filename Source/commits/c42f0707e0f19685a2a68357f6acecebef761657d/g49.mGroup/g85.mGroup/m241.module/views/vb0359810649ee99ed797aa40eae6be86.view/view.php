<?php
//#section#[header]
// Module Declaration
$moduleID = 241;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("UI", "Developer");
importer::import("UI", "Presentation");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \UI\Presentation\notification;
use \UI\Developer\devTabber;
use \DEV\Modules\module;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	// Create report notification
	$reportNtf = new notification();
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add form post error header
		$reportNtf->build($type = "error", $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.invalidate");
		$reportNtf->append($reportMessage);
		
		$notification = $reportNtf->get();
		return devTabber::getNotificationResult($notification, FALSE);
	}
	
	// Initialize view
	$viewModuleID = engine::getVar('viewModuleID');
	$viewID = engine::getVar('viewID');
	
	$moduleObject = new module($viewModuleID);
	$viewObject = $moduleObject->getView("", $viewID);
	$status = $viewObject->updateView($_POST['viewHtml'], $_POST['viewCss']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// TEMP
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
	else if ($status === FALSE)
	{
		// TEMP
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
	}
	else
	{
		$message = "err.save_error";
		$reportNtf->build($type = "warning", $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}
//#section_end#
?>