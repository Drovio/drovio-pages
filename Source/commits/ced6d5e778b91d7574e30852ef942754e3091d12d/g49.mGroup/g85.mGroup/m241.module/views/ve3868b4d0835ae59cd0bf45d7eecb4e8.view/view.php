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
importer::import("DEV", "Modules");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Developer\devTabber;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\notification;
use \DEV\Modules\module;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	// Initialize view
	$viewModuleID = engine::getVar('viewModuleID');
	$viewID = engine::getVar('viewID');
	
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
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// If everything is ok, validate dependencies
		$depStatus = $viewObject->validateSingleDependencies();
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