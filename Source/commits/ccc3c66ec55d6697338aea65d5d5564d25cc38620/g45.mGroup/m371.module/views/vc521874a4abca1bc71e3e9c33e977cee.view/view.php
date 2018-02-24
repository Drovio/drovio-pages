<?php
//#section#[header]
// Module Declaration
$moduleID = 371;

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
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);

// Get action factory
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "uc");

// Add a hello world dynamic content
$hw = DOM::create("p", "Hello World!");
$pageContent->append($hw);

// Return output
return $pageContent->getReport();
//#section_end#
?>