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
importer::import("ESS", "Protocol");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\HTMLServerReport;
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
$vcs = new vcs($projectID);

// Get authors
$authors = $vcs->getAuthors();

// Get branchName
$branchName = $_GET['bn'];
$page = (isset($_GET['page']) ? $_GET['page'] : 0);

// Get branch commits
$commits = $vcs->getBranchCommits($branchName);
$commits = array_reverse($commits, TRUE);

// If page is > 0, then holder is commitContainer.
$holder = "";
if ($page > 0 || isset($_GET['pagination']))
	$holder = ".commitsInnerContainer";
	
$commitsContainer = DOM::create("div", "", "", "commitsInnerContainer");
$info = array();
$info['page'] = $page;
$info['bn'] = $branchName;
$info['totalPages'] = intval(round(count($commits) / 30));
DOM::data($commitsContainer, "info", $info);
$pageContent->append($commitsContainer);

// Get first page of commits
$startIndex = $page * 30;
$pageCommits = array_slice($commits, $startIndex, 30);

// Get commit Groups
$commitGroups = array();
foreach ($pageCommits as $commitID => $commitData)
{
	// Get date
	$groupDate = date("M d, Y", $commitData['time']);
	$commitGroups[$groupDate][$commitID] = $commitData;
}

foreach ($commitGroups as $groupDate => $commitListData)
{
	//$tog = new toggler();
	$commitContainer = DOM::create("div", "", "", "commitGroupContainer");
	DOM::append($commitsContainer, $commitContainer);
	
	// Create commitGroup header
	$commitGroupHeader = DOM::create("div", $groupDate, "", "commitGroupHeader");
	DOM::append($commitContainer, $commitGroupHeader);
	
	// Create commitList body
	$commitList = DOM::create("div", "", "", "commitList");
	DOM::append($commitContainer, $commitList);
	
	// List all commits
	foreach ($commitListData as $commitID => $commitData)
	{
		// Set toggler data
		$cnt = DOM::create("span", $commitData['summary']);
		$togHeader = DOM::create("div", $cnt);
		
		$author = DOM::create("div", $commitData['author'].", ", "", "cAuthor");
		DOM::append($togHeader, $author);
		$commitDate = DOM::create("span", "commited ", "", "cDate");
		DOM::append($author, $commitDate);
		$liveDate = datetimer::live($commitData['time']);
		DOM::append($commitDate, $liveDate);
		
		// Commit Body
		$togBody = DOM::create("div", "", "", "commitInfo");
		
		// Header
		$header = DOM::create("div", "", "", "infoHeader");
		DOM::append($togBody, $header);
		
		// Commit ID
		$cidContext = DOM::create("p", "Commit ID: ".$commitID);
		DOM::append($togBody, $cidContext);
		
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
			$path = DOM::create("span", directory::normalize("/".$itemInfo['path']."/".$itemInfo['name']));
			$itemLi = DOM::create("li", $path);
			DOM::append($itemList, $itemLi);
		}
		
		// Build toggler
		$tog = new toggler();
		$commitViewer = $tog->build($id = $commitID, $togHeader, $togBody, $open = FALSE)->get();
		DOM::append($commitList, $commitViewer);
	}
	
	//$togHeader = DOM::create("span", $groupDate);
	//$commitGroup = $tog->build($id = "group_".$groupDate, $togHeader, $commitList, $open = TRUE)->get();
	//HTML::addClass($commitGroup, "commitGroupToggler");
	//DOM::append($commitsContainer, $commitGroup);
}

if (count($commits) == 0)
{
	// No commits notification
	$noCommits = DOM::create("p", "There are no commits in this repository yet.");
	DOM::append($commitsContainer, $commitViewer);
}

$commitCount = count($commits);
if (empty($holder) && $commitCount > 30)
{
	$controlsContainer = DOM::create("div", "", "", "controls");
	$pageContent->append($controlsContainer);

	// Pagination
	$pagination = DOM::create("div", "", "", "pagination");
	DOM::append($controlsContainer, $pagination);
	
	if ($page > 0)
		$extraClass = "active";
	$newerBtn = DOM::create("span", "<< Newer", "", trim("navBtn newer ".$extraClass));
	DOM::append($pagination, $newerBtn);
	
	if ($page * 30 < $commitCount)
		$extraClass = "active";
	$olderBtn = DOM::create("span", "Older >>", "", trim("navBtn older ".$extraClass));
	DOM::append($pagination, $olderBtn);
	
	
	// Pages
	$pagesInfo = DOM::create("div", "", "", "pagesInfo");
	DOM::append($controlsContainer, $pagesInfo);
	
	$startCount = $page*30+1;
	$endCount = ($commitCount < ($page + 1)*30 ? $commitCount : ($page + 1)*30);
	$text = DOM::create("span", "Displaying ".$startCount." to ".$endCount." commits, from ".$commitCount." total.");
	DOM::append($pagesInfo, $text);
}
else
{	// Page Info Text
	$startCount = $page*30+1;
	$endCount = ($commitCount < ($page + 1)*30 ? $commitCount : ($page + 1)*30);
	$text = DOM::create("span", "Displaying ".$startCount." to ".$endCount." commits, from ".$commitCount." total.");
		
	$pageContent->addReportContent($text, ".pagesInfo", $method = HTMLServerReport::REPLACE_METHOD);
}

// Return output
return $pageContent->getReport($holder);
//#section_end#
?>