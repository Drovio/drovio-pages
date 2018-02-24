<?php
//#section#[header]
// Module Declaration
$moduleID = 358;

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
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\apps\application;
use \UI\Modules\MContent;
use \DEV\Apps\application as devApp;
use \DEV\Projects\projectLibrary;
use \DEV\Projects\projectReadme;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get project id and name
$applicationID = engine::getVar('id');
$applicationName = engine::getVar('name');

// Get application info from project
$app = new devApp($applicationID, $applicationName);
$appInfo = $app->info();

// Get project data
$applicationID = $appInfo['id'];

// Build the module content
$pageContent->build("", "appInfoContainer", TRUE);

// Get application info
$version = projectLibrary::getLastProjectVersion($applicationID);
$applicationPath = application::getApplicationPath($applicationID, $version);

// Load document
$projectReadme = new projectReadme($applicationPath, TRUE);
$readmeContent = $projectReadme->load();
$readmeContainer = HTML::select(".appInfoContainer .appInfo")->item(0);
if (!empty($readmeContent))
	HTML::innerHTML($readmeContainer, $readmeContent);

// Return output
return $pageContent->getReport();
//#section_end#
?>