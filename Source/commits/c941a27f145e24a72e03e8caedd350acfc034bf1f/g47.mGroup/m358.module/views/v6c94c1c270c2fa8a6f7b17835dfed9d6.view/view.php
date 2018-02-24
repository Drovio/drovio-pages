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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\apps\application;
use \UI\Modules\MContent;
use \DEV\Apps\application as devApp;

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
$appInfo = application::getApplicationInfo($applicationID);

// Build the module content
$pageContent->build("", "appChangelogContainer", TRUE);

// Set title
$title = $pageContent->getLiteral("lbl_changelogHeader");
$header = HTML::select(".appChangelog .header")->item(0);
DOM::append($header, $title);

// Add changelog
$changelog = HTML::select(".appChangelog .changelog")->item(0);
DOM::innerHTML($changelog, $appInfo['changelog']);

// Return output
return $pageContent->getReport();
//#section_end#
?>