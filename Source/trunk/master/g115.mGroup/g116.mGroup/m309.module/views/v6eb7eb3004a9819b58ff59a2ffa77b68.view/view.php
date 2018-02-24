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
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "vcsStatisticsContainer");

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
if (!$projectInfo['open'])
{
	// Create error notification
	
	// Return output
	return $pageContent->getReport();
}

// Get vcs object
$vcs = new vcs($projectID);

// Get branch commits
$branchName = engine::getVar('bn');
$branchName = empty($branchName) ? vcs::MASTER_BRANCH : $branchName;
$commits = $vcs->getBranchCommits($branchName);
$totalCount = count($commits);

// Get commit authors
$authors = array();
foreach ($commits as $commitID => $commitData)
{
	// Get author id
	$authorID = $commitData['author_id'];
	$authors[$authorID]['name'] = $commitData['author'];
	$authors[$authorID]['count']++;
}

foreach ($authors as $authorID => $authorData)
{
	$authorViewer = getAuthorViewer($authorID, $authorData['name'], $authorData['count'], $totalCount);
	$pageContent->append($authorViewer);
}


return $pageContent->getReport();

function getAuthorViewer($authorID, $authorName, $commitCount, $totalCount)
{
	// Create commit viewer
	$authorViewer = DOM::create("div", "", "", "authViewer");
	
	// Create header
	$vHeader = DOM::create("div", "", "", "authHeader");
	DOM::append($authorViewer, $vHeader);
	
	// Set header image
	$initials = getAuthInitials($authorName);
	$img = DOM::create("div", $initials, "", "authImg");
	DOM::append($vHeader, $img);
	
	// Commit description
	$author = DOM::create("h3", $authorName, "", "authName");
	DOM::append($vHeader, $author);
	
	// Counters
	$percentage = number_format(($commitCount/$totalCount) * 100, 2);
	$counters = DOM::create("div", $commitCount." Commits / ".($percentage)."%", "", "counters");
	DOM::append($authorViewer, $counters);
	
	// Set percentage bar
	$bar = DOM::create("span", "", "", "bar");
	DOM::attr($bar, "style", "width: ".$percentage."%");
	DOM::append($authorViewer, $bar);
	
	// Return commit viewer
	return $authorViewer;
}

function getAuthInitials($author)
{
	// Initialize
	$initials = "";
	
	// Break words
	$words = explode(" ", $author);

	// Get first letter
	foreach ($words as $word)
		$initials .= substr($word, 0, 1);

	// Get upper case
	return strtoupper($initials);
}
//#section_end#
?>