<?php
//#section#[header]
// Module Declaration
$moduleID = 286;

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
importer::import("DEV", "Websites");
importer::import("UI", "Developer");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Presentation\notification;
use \UI\Developer\devTabber;
use \DEV\Websites\pages\wsPage;

// Get page variables
$websiteID = engine::getVar('id');
$pageFolder = engine::getVar('folder');
$pageName = engine::getVar('name');
$wsPage = new wsPage($websiteID, $pageFolder, $pageName);

if (engine::isPost())
{
	// Get Source Code
	$sourceCode = $_POST['pageSource'];
	
	// Update Dependencies and Source Code
	$status = $wsPage->updateDependencies($_POST['wsdk_dependencies'], $_POST['ws_dependencies']);
	$status = $wsPage->updatePHPCode($sourceCode);

	// Update page settings
	$wsPage->updateSettings($_POST['settings']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// If everything is ok, validate dependencies
		$depStatus = $wsPage->validateSingleDependencies();
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
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
		$extraContainer = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$reportNtf->append($extraContainer);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE), "pgsTabber");
}
//#section_end#
?>