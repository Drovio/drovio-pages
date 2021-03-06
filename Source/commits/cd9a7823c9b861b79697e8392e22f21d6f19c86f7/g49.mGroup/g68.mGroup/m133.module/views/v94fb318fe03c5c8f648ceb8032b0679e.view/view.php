<?php
//#section#[header]
// Module Declaration
$moduleID = 133;

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
importer::import("UI", "Forms");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \UI\Forms\formReport\formNotification;
use \DEV\Apps\test\viewTester;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$appID = $_POST['appID'];
	
	// Get activated packages
	$views = array();
	
	// Activate Packages
	if (is_array($_POST['view']))
		foreach ($_POST['view'] as $view => $value)
			$views[] = $view;
		
	// Activate packages
	$status = viewTester::activate($appID, $views);
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
//#section_end#
?>