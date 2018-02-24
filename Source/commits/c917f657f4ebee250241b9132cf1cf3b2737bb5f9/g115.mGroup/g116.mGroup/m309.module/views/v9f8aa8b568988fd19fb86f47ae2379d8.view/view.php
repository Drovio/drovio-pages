<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "projectReleases");

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

// Check if project is open
if (!$projectInfo['public'])
{
	// Create error notification
	
	// Return output
	return $pageContent->getReport();
}

// Get project releases
$releases = $project->getReleases();
foreach ($releases as $releaseInfo)
{
	// Get only approved releases
	if ($releaseInfo['status_id'] != "2")
		continue;
	
	// Create release tile
	$releaseTile = getReleaseTile($moduleID, $releaseInfo);
	$pageContent->append($releaseTile);
}

if (count($releases) == 0)
{
	// No commits notification
	$title = moduleLiteral::get($moduleID, "lbl_no_releases");
	$noReleases = DOM::create("p", $title);
	$pageContent->append($noReleases);
}

// Return output
return $pageContent->getReport();


function getReleaseTile($moduleID, $preleaseInfo)
{
	// Create tile
	$releaseTile = DOM::create("div", "", "", "releaseTile");
	
	$relInnerContainer = DOM::create("div", "", "", "relInner");
	DOM::append($releaseTile, $relInnerContainer);
	
	$relHeader = DOM::create("div", "", "", "releaseHeader");
	DOM::append($relInnerContainer, $relHeader);
	
	$ico = DOM::create("div", $preleaseInfo['version'], "", "relIco");
	DOM::append($relHeader, $ico);
	
	// Set release title
	$releaseTitle = DOM::create("h4", $preleaseInfo['title'], "", "releaseTitle");
	DOM::append($relHeader, $releaseTitle);
	
	// Set release date
	$date = datetimer::live($preleaseInfo['time_created']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($relHeader, $releaseDate);
	
	// Show changelog
	$title = moduleLiteral::get($moduleID, "lbl_showChangelog");
	$showLog = DOM::create("div", $title, "", "show_changelog");
	DOM::append($relHeader, $showLog);
	
	// Create changelog body
	$changelog = DOM::create("div", $preleaseInfo['changelog'], "", "changelog");
	DOM::append($relInnerContainer, $changelog);
	
	// Return tile
	return $releaseTile;
}
//#section_end#
?>