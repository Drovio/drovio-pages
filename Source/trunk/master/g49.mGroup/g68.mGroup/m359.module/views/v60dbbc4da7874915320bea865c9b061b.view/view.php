<?php
//#section#[header]
// Module Declaration
$moduleID = 359;

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
importer::import("BSS", "Market");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \BSS\Market\appMarket;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build the module content
$pageContent->build("", "enpAnalyticsPage", TRUE);

// Get statistics
$sessionStats = appMarket::getApplicationMarketStatistics($projectID);

// Purchases
$sessionContainer = HTML::select(".enpAnalytics .sessions")->item(0);
$totalCount = 0;
foreach ($sessionStats as $statsInfo)
{
	$sr = DOM::create("div", "", "", "sr");
	DOM::append($sessionContainer, $sr);
	
	$attr = array();
	$attr['version'] = $statsInfo['version'];
	$title = $pageContent->getLiteral("lbl_appVersion", $attr);
	$key = DOM::create("div", $title, "", "key");
	DOM::append($sr, $key);
	
	$value = DOM::create("div", $statsInfo['team_count'], "", "value");
	DOM::append($sr, $value);
	
	$totalCount += $statsInfo['team_count'];
}

// Total
$totalValue = HTML::select(".enpAnalytics .sessions .sr.total .value")->item(0);
DOM::innerHTML($totalValue, $totalCount);

// Return output
return $pageContent->getReport();
//#section_end#
?>