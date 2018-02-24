<?php
//#section#[header]
// Module Declaration
$moduleID = 250;

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
importer::import("API", "Resources");
importer::import("UI", "Modules");
importer::import("DEV", "Prototype");
//#section_end#
//#section#[code]
use \API\Resources\DOMParser;
use \UI\Modules\MContent;
use \DEV\Prototype\sourceMap;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "coreMetrics", TRUE);



// Load release sdk map file
$project = new project(1);
$releaseSDKMapFile = $project->getReleaseFolder();
$srcMap = new sourceMap($modelFolder);


// Get all objects and add model
$fullObjects = array();
$libraries = $srcMap->getLibraryList();
foreach ($libraries as $library)
{
	$packages = $srcMap->getPackageList($library);
	foreach ($packages as $package)
	{
		$objects = $srcMap->getObjectList($library, $package);
		foreach ($objects as $object)
			$fullObjects[] = $library."/".$package."/".str_replace("::", "/", $object['namespace'])."/".$object['name'];
	}
}


// Return output
return $pageContent->getReport();
//#section_end#
?>