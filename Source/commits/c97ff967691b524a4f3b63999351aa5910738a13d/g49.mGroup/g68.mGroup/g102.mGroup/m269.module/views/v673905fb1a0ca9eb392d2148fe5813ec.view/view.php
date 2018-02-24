<?php
//#section#[header]
// Module Declaration
$moduleID = 269;

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
importer::import("DEV", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Presentation\notification;
use \UI\Developer\devTabber;
use \DEV\Apps\views\appView;

if (engine::isPost())
{
	// Initialize application view
	$appID = engine::getVar('id');
	$viewFolder = engine::getVar('parent');
	$viewName = engine::getVar('name');
	$appView = new appView($appID, $viewFolder, $viewName);
	
	// Get Source Code
	$sourceCode = $_POST['viewSource'];
	
	// Update Info and Source Code
	$status = $appView->updateInfo($_POST['sdk_dependencies'], $_POST['app_dependencies']);
	$status = $appView->updatePHPCode($sourceCode);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// If everything is ok, validate dependencies
		$depStatus = $appView->validateSingleDependencies();
		if ($depStatus !== TRUE)
		{
			// Warn user for missing dependencies
			$reportNtf->build($type = notification::WARNING, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
			$reportMessage = $reportNtf->getMessage("success", "success.save_success");
			
			// Add details
			$extraContainer = DOM::create("div", "", "", "warning_message");
			$header = DOM::create("h4", "There are missing dependencies in this document.");
			DOM::append($extraContainer, $header);
			$list = DOM::create("ul");
			DOM::append($extraContainer, $list);
			foreach ($depStatus as $library => $packages)
				foreach ($packages as $package => $nothing)
				{
					$extra = DOM::create("li", $library." -> ".$package);
					DOM::append($extraContainer, $extra);
				}
		}
		else
		{
			// Get all OK notification
			$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
			$reportMessage = $reportNtf->getMessage("success", "success.save_success");
		}
	}
	else if ($status === FALSE)
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	else
	{
		$reportNtf->build($type = notification::WARNING, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = DOM::create("h2", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	if (isset($extraContainer))
		$reportNtf->append($extraContainer);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}
//#section_end#
?>