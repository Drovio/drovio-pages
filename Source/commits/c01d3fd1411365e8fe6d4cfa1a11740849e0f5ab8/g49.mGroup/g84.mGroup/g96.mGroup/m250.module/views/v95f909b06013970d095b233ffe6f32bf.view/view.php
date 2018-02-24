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
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
importer::import("DEV", "Prototype");
//#section_end#
//#section#[code]
use \API\Resources\DOMParser;
use \UI\Modules\MContent;
use \DEV\Prototype\sourceMap;
use \DEV\Prototype\classObject;
use \DEV\Projects\project;
use \DEV\Version\vcs;
//getMetrics($metFile)
// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "coreMetricsPage", TRUE);


// Load release sdk map file
$vcs = new vcs(1);
$releaseSDKPath = $vcs->getCurrentRelease()."/SDK/";
$srcMap = new sourceMap($releaseSDKPath);

// Store global metrics
$globalMetrics = array();

// Get all objects and add model
$fullObjects = array();
$libraries = $srcMap->getLibraryList();
$globalMetrics['libCount'] = count($libraries);
foreach ($libraries as $library)
{
	$packages = $srcMap->getPackageList($library);
	$globalMetrics['pkgCount'] += count($packages);
	foreach ($packages as $package)
	{
		$objects = $srcMap->getObjectList($library, $package);
		$globalMetrics['objCount'] += count($objects);
		foreach ($objects as $object)
		{
			// Get metrics
			$objectReleasePath = $releaseSDKPath."/".$library."/".$package."/".str_replace("::", "/", $object['namespace'])."/".$object['name'].".object/src/metrics.xml";
			$metrics = classObject::getMetrics($objectReleasePath);
			
			// Sum up
			$globalMetrics['LOC'] += $metrics['LOC'];
			$globalMetrics['CLOC'] += $metrics['CLOC'];
			$globalMetrics['SLOC-P'] += $metrics['SLOC-P'];
		}
	}
}

// Set values
$ms = array();
$ms['libs'] = "libCount";
$ms['pkgs'] = "pkgCount";
$ms['objs'] = "objCount";
$ms['loc'] = "LOC";
$ms['cloc'] = "CLOC";
$ms['ploc'] = "SLOC-P";
foreach ($ms as $name => $metric)
{
	$elem = HTML::select(".metrics .".$name." .value")->item(0);
	HTML::innerHTML($elem, number_format($globalMetrics[$metric]));
}

// Return output
return $pageContent->getReport();
//#section_end#
?>