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
importer::import("UI", "Interactive");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \UI\Interactive\forms\switchButton;
use \DEV\Apps\test\viewTester;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$appID = $_POST['appID'];
	if (viewTester::status($appID) === FALSE)
	{
		viewTester::activate($appID);
		$status = TRUE;
	}
	else
	{
		viewTester::deactivate($appID);
		$status = FALSE;
	}
	
	// Return switchButton report
	return switchButton::getReport($status);
}
//#section_end#
?>