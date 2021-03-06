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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \API\Resources\geoloc\datetimer;
use \UI\Html\HTMLContent;
use \UI\Presentation\togglers\toggler;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new HTMLContent();
$pageContent->build("", "vcsCommits");

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

// Get authors
$authors = $vcs->getAuthors();

// Get branchName
$branchName = $_GET['bn'];
$page = (isset($_GET['page']) ? $_GET['page'] : 0);

// Get branch commits
$commits = $vcs->getBranchCommits($branchName);
$commits = array_reverse($commits, TRUE);

// Get first page of commits
//$startIndex = $page * 30;
//$pageCommits = array_slice($commits, $startIndex, 30);
foreach ($commits as $commitID => $commitData)
{
	// Create toggler
	$tog = new toggler();
	
	// Set toggler data
	$commitDate = datetimer::live($commitData['time']);
	$togHeader = DOM::create("span", " - ");
	DOM::prepend($togHeader, $commitDate);
	$cnt = DOM::create("span", $commitData['summary']." - by ");
	DOM::append($togHeader, $cnt);
	$cnt = DOM::create("b", $authors[$commitData['author']]);
	DOM::append($togHeader, $cnt);
	$togBody = DOM::create("div", "", "", "commitInfo");
	
	// Header
	$header = DOM::create("div", "", "", "infoHeader");
	DOM::append($togBody, $header);
	
	// Commit Description
	if (!empty($commitData['description']))
	{
		$title = DOM::create("p", "Commit Description:");
		DOM::append($togBody, $title);
		$cDesc = DOM::create("p", $commitData['description'], "", "cDesc");
		DOM::append($togBody, $cDesc);
	}
	
	// Item List
	$title = DOM::create("p", "Items affected:");
	DOM::append($togBody, $title);
	$itemList = DOM::create("ol", "", "", "commitItems");
	DOM::append($togBody, $itemList);
	
	// Get commit items
	$commitItems = $vcs->getCommitItems($commitID);
	foreach ($commitItems as $itemID => $itemInfo)
	{
		$path = directory::normalize("/".$itemInfo['path']."/".$itemInfo['name']);
		$itemLi = DOM::create("li", $path);
		DOM::append($itemList, $itemLi);
	}
	
	// Build toggler
	$commitViewer = $tog->build($id = $commitID, $togHeader, $togBody, $open = FALSE)->get();
	$pageContent->append($commitViewer);
}

if (count($commits) == 0)
{
	// No commits notification
	$noCommits = DOM::create("p", "There are no commits in this repository yet.");
	$pageContent->append($noCommits);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>