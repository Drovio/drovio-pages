<?php
//#section#[header]
// Module Declaration
$moduleID = 251;

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
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Model\modules\mGroup;
use \UI\Modules\MContent;
use \DEV\Modules\module as devModule;
use \DEV\Modules\components\mView;
use \DEV\Version\vcs;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "modulesMetricsPage", TRUE);

// Get release path
$vcs = new vcs(2);
$releaseModulesPath = $vcs->getCurrentRelease();

// Store global metrics
$globalMetrics = array();

// Get all modules
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_all_modules");
$result = $dbc->execute($q);
$globalMetrics['moduleCount'] = $dbc->get_num_rows($result);
while ($module = $dbc->fetch($result))
{
	// Get module path
	$modulePath = $releaseModulesPath."/".mGroup::getTrail($module['group_id'])."/".module::getDirectoryName($module['id']);
	
	// Get views
	$mdl = new devModule($module['id']);
	$views = $mdl->getViews();
	$globalMetrics['viewCount'] += count($views);
	foreach ($views as $viewID => $viewName)
	{
		// Get the view path
		$viewPath = $modulePath."/views/".$viewID.".view";
		$metrics = mView::getMetrics($viewPath."/metrics.xml");
		
		// Sum up
		$globalMetrics['LOC'] += $metrics['LOC'];
		$globalMetrics['CLOC'] += $metrics['CLOC'];
		$globalMetrics['SLOC-P'] += $metrics['SLOC-P'];
	}
}


// Set values
$ms = array();
$ms['modules'] = "moduleCount";
$ms['mviews'] = "viewCount";
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