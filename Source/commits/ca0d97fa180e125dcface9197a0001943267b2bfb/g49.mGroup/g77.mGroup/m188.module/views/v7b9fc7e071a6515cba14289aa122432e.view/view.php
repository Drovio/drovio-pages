<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \API\Resources\geoloc\datetimer;
use \UI\Html\HTMLContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new HTMLContent();
$pageContent->build("", "vcsReleases");

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}

// Get vcs object
$repository = $project->getRepository();
$vcs = new vcs($repository);



// Get releases
$branchName = $_GET['bn'];
$releases = $vcs->getReleases();
$releases = $releases[$branchName];
// List all releases
$packages = array_reverse($releases['packages']);
foreach ($packages as $packageID => $packageData)
{
	$packageContainer = DOM::create("div", "", "package_".$packageID, "branchPackage");
	$pageContent->append($packageContainer);
	
	// Get releases
	$releases = array_reverse($packageData['releases']);
	
	// Package Row
	$packageRow = DOM::create("div", "", "package_".$branchName."_".$packageID, "releaseRow packageRow");
	DOM::append($packageContainer, $packageRow);
	
	$latestRelease = reset($releases);
	$date = datetimer::live($latestRelease['time']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($packageRow, $releaseDate);
	
	$releaseTitle = DOM::create("h4", "Version ".$latestRelease['version']." - ".$latestRelease['title']." (build ".$latestRelease['build'].")", "", "releaseTitle");
	DOM::append($packageRow, $releaseTitle);
	
	$historyContainer = DOM::create("span", "", "", "historySpan");
	DOM::append($releaseTitle, $historyContainer);
	
	$showHistory = DOM::create("span", " - show history", "", "showHistory");
	DOM::append($historyContainer, $showHistory);
	
	$hideHistory = DOM::create("span", " - hide history", "", "hideHistory");
	DOM::append($historyContainer, $hideHistory);
	
	// Version history
	$versionHistory = DOM::create("div", "", "package_".$packageID."_history", "versionHistory");
	DOM::append($packageContainer, $versionHistory);
	
	// Show detailed releases
	$releases = array_slice($releases, 1, count($releases)-1, TRUE);
	foreach ($releases as $releaseID => $releaseInfo)
	{
		$releaseRow = DOM::create("div", "", "release_".$branchName."_".$releaseID, "releaseRow");
		DOM::append($versionHistory, $releaseRow);
		
		$date = datetimer::live($releaseInfo['time']);
		$releaseDate = DOM::create("div", $date, "", "releaseDate");
		DOM::append($releaseRow, $releaseDate);
		
		$releaseTitle = DOM::create("h4", $releaseInfo['title']." (v".$releaseInfo['version'].".".$releaseInfo['build'].")", "", "releaseTitle");
		DOM::append($releaseRow, $releaseTitle);
	}
}

// No releases notification
if (count($releases) == 0)
{
	$noReleases = DOM::create("p", "There are no releases in this repository yet.");
	DOM::append($navContainer, $noReleases);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>