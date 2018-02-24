<?php
//#section#[header]
// Module Declaration
$moduleID = 101;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("AEL", "Mail");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("ESS", "Environment");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Login");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \UI\Login\loginDialog;

$pageContent = new MContent($moduleID);
$pageContent->build("", "testingPage");

$lg = new loginDialog();
$dialog = $lg->build()->get();
$pageContent->append($dialog);

return $pageContent->getReport();
//#section_end#
?>