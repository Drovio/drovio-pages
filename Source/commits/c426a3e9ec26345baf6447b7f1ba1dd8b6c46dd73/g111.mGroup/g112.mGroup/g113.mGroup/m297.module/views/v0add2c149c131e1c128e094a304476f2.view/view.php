<?php
//#section#[header]
// Module Declaration
$moduleID = 297;

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
//#section_end#
//#section#[code]
use \ESS\Protocol\loaders\AppLoader;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;

// Initialize application
$appID = $_REQUEST['id'];
$appName = $_REQUEST['name'];
$app = new application($appID, $appName);
$appID = $app->getID();


// Validate api call
$api_key = $_REQUEST['key'];





// Get tester mode levels


// Get view name to load
$viewName = $_REQUEST['vnm'];


// Activate tester mode for application
appTester::setPublisherLock(FALSE);

// Get initial application view
return AppLoader::load($appID, $viewName);
//#section_end#
?>