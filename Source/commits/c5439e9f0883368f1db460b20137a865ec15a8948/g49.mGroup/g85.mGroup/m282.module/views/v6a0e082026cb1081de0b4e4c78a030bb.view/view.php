<?php
//#section#[header]
// Module Declaration
$moduleID = 282;

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
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \UI\Interactive\forms\switchButton;
use \DEV\Modules\test\mrsrcTester;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get status
	$status = (mrsrcTester::status() == FALSE);

	// Activate or deactivate
	if (mrsrcTester::status() == FALSE)
		mrsrcTester::activate();
	else
		mrsrcTester::deactivate();

	// Return report status
	return switchButton::getReport($status);
}
return FALSE;
//#section_end#
?>