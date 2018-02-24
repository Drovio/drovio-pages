<?php
//#section#[header]
// Module Declaration
$moduleID = 283;

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
importer::import("DEV", "Core");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \UI\Interactive\forms\switchButton;
use \DEV\Core\test\rsrcTester;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get status
	$status = (rsrcTester::status() == FALSE);

	// Activate or deactivate
	if (rsrcTester::status() == FALSE)
		rsrcTester::activate();
	else
		rsrcTester::deactivate();

	// Return report status
	return switchButton::getReport($status);
}
return FALSE;
//#section_end#
?>