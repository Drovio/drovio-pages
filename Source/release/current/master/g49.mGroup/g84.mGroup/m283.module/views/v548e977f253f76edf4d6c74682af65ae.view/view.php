<?php
//#section#[header]
// Module Declaration
$moduleID = 283;

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
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \UI\Interactive\forms\switchButton;
use \DEV\Core\test\ajaxTester;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get status
	$status = (ajaxTester::status() == FALSE);
	
	// Activate or deactivate
	if (ajaxTester::status() == FALSE)
		ajaxTester::activate();
	else
		ajaxTester::deactivate();
	
	// Return report status
	return switchButton::getReport($status);
}
return FALSE;
//#section_end#
?>