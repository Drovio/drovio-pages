<?php
//#section#[header]
// Module Declaration
$moduleID = 264;

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
importer::import("DEV", "Documentation");
importer::import("UI", "Developer");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Presentation\notification;
use \UI\Developer\devTabber;
use \DEV\Apps\application;
use \DEV\Apps\source\srcObject;
use \DEV\Documentation\classComments;
use \DEV\Documentation\classDocumentor;

if (engine::isPost())
{
	// Get application
	$appID = engine::getVar('id');
	$app = new application($appID);
	$appInfo = $app->info();
	
	// Initialize source object
	$sdkObj = new srcObject($appID, $_POST['pkg'], $_POST['ns'], $_POST['oid']);
	
	// Get Source Code
	$sourceCode = $_POST['wideContent'];
	
	// Get Documentation
	$manual = $_POST['classXMLModel'];
	
	// Update Documentation + Pretify Source Code (with comments)
	if (classDocumentor::isValidDocumentation($manual))
	{
		// Copyright signature
		$copyright = $appInfo['name'];
		$copyright = (empty($copyright) ? $appInfo['title'] : $copyright);
		
		// Update source documentation
		$sdkObj->updateSourceDoc($manual);
		$updated_manual = $sdkObj->getSourceDoc();
		$strippedCode = classComments::stripSourceCode($sourceCode);
		$sourceCode = classComments::pretifySourceCode($strippedCode, $updated_manual, $sdkObj->getLibrary(), $sdkObj->getPackage(), $sdkObj->getNamespace(), $copyright);
	}
	
	// Update Source Code
	$status = $sdkObj->updateSourceCode($sourceCode);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
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
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}
//#section_end#
?>