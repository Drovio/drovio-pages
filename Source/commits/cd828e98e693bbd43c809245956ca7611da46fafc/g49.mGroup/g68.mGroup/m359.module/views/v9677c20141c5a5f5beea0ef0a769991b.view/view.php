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
importer::import("API", "Model");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\apps\appSessionManager;
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
$pageContent->build("", "appSessionsPage", TRUE);

// Get application session manager
$asm = appSessionManager::getInstance();

// Get statistics
$sessionStats = $asm->getApplicationStats($projectID);

// Guests
$stats = array();
$stats['guests'] = "guests";
$stats['regs'] = "users";
foreach ($stats as $name => $key)
{
	$elem = HTML::select(".appSessions .sessions .".$name." .value")->item(0);
	HTML::innerHTML($elem, number_format($sessionStats[$key]));
}


// Get detailed statistics
$detailedStats = $asm->getApplicationStatsDetails($projectID);
if (count($detailedStats) > 0)
{
	$gridList = new dataGridList();
	$detailedList = $gridList->build($id = "sess_detailed", $checkable = FALSE)->get();
	$detailedContainer = HTML::select(".appSessions .details")->item(0);
	DOM::append($detailedContainer, $detailedList);
	
	// Set headers
	$headers = array();
	$headers[] = "Type";
	$headers[] = "Version";
	$headers[] = "User Agent";
	$headers[] = "Time Updated";
	$gridList->setHeaders($headers);
	
	// List details
	foreach ($detailedStats as $stats)
	{
		$row = array();
		$row[] = (empty($stats['account_id']) ? "Guest" : "Registered");
		$row[] = $stats['version'];
		$row[] = $stats['user_agent'];
		$row[] = date("M d, Y", $stats['time_updated']);
		
		$gridList->insertRow($row);
	}
}

// Return output
return $pageContent->getReport();
//#section_end#
?>