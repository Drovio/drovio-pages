<?php
//#section#[header]
// Module Declaration
$moduleID = 244;

// Inner Module Codes
$innerModules = array();
$innerModules['publisher'] = 261;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

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

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "releaseLogPage", TRUE);


// Set publisher action
// Set literal
$publisher = HTML::select(".releaseLog .hd .publisher")->item(0);

// Set publish action
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($publisher, $innerModules['publisher'], "", "", $attr, $loading = TRUE);
	
// Get releases
$releases = $project->getReleases();


// Show release log
$listContainer = HTML::select(".releaseLog .list")->item(0);
foreach ($releases as $release)
{
	$releaseContainer = DOM::create("div", "", "release_".$release['version'], "prel");
	DOM::append($listContainer, $releaseContainer);
	
	// Release Row
	$releaseRow = DOM::create("div", "", "", "releaseRow");
	DOM::append($releaseContainer, $releaseRow);
	
	$releaseHeader = DOM::create("div", "", "", "rhd");
	DOM::append($releaseRow, $releaseHeader);
	
	$releaseTitle = DOM::create("h4", "Version v".$release['version'], "", "releaseTitle");
	DOM::append($releaseHeader, $releaseTitle);
	
	$date = datetimer::live($release['time_created']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($releaseHeader, $releaseDate);
	
	// Status
	$rStatus = DOM::create("div", "", "", "rStatus st".$release['status_id']);
	DOM::append($releaseHeader, $rStatus);
	
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($rStatus, $ico);
	
	$title = moduleLiteral::get($moduleID, "lbl_rStatus_".$release['status_id']);
	$statusTitle = DOM::create("div", $title, "", "status");
	DOM::append($rStatus, $statusTitle);
	
	
	// Information, changelog and comments
	$releaseInfo = DOM::create("div", "", "", "releaseInfo");
	DOM::append($releaseRow, $releaseInfo);
	
	$title = moduleLiteral::get($moduleID, "lbl_changelog");
	$header = DOM::create("h4", $title);
	DOM::append($releaseInfo, $header);
	$changelog = DOM::create("p", $release['changelog'], "", "releaseChangelog");
	DOM::append($releaseInfo, $changelog);
	
	$title = moduleLiteral::get($moduleID, "lbl_reviewComments");
	$header = DOM::create("h4", $title);
	DOM::append($releaseInfo, $header);
	$comments = DOM::create("p", $release['comments'], "", "reviewComments");
	DOM::append($releaseInfo, $comments);
}



// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>