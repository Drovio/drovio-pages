<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "vcsCommitsContainer", TRUE);

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
$authors = $vcs->getAuthors();

// Get branchName
$branchName = $_GET['bn'];
$commits = $vcs->getBranchCommits($branchName);

$currentPage = (isset($_GET['page']) ? $_GET['page'] : 0);
$pageNumCommits = 30;
$pageCount = ceil(count($commits) / $pageNumCommits);

// Get current page of commits
$startIndex = $currentPage * $pageNumCommits;
$pageCommits = array_slice($commits, $startIndex, $pageNumCommits);

// Get commit Groups
$commitGroups = array();
foreach ($pageCommits as $commitID => $commitData)
{
	// Get date
	$groupDate = date("M d, Y", $commitData['time']);
	$commitGroups[$groupDate][$commitID] = $commitData;
}

$commitsContainer = HTML::select(".commitsInnerContainer")->item(0);
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
}

if (count($commits) == 0)
{
	// No commits notification
	$title = moduleLiteral::get($moduleID, "lbl_no_commits");
	$noCommits = DOM::create("p", $title);
	DOM::append($commitsContainer, $commitViewer);
}

$totalCommitCount = count($commits);
$newerBtn = HTML::select(".navBtn.newer")->item(0);
$olderBtn = HTML::select(".navBtn.older")->item(0);

// Set active buttons
if ($currentPage > 0)
{
	HTML::addClass($newerBtn, "active");
	$attr = array();
	$attr['bn'] = $branchName;
	$attr['page'] = $currentPage - 1;
	$actionFactory->setModuleAction($newerBtn, $moduleID, "repositoryCommits", "#commitsContainer", $attr);
}

if (($currentPage + 1) * $pageNumCommits < $totalCommitCount)
{
	HTML::addClass($olderBtn, "active");
	$attr = array();
	$attr['bn'] = $branchName;
	$attr['page'] = $currentPage + 1;
	$actionFactory->setModuleAction($olderBtn, $moduleID, "repositoryCommits", "#commitsContainer", $attr);
}

// Set page display
$startCount = $currentPage * $pageNumCommits + 1;
$endCount = ($totalCommitCount < ($currentPage + 1) * $pageNumCommits ? $totalCommitCount : ($currentPage + 1) * $pageNumCommits);
$attr = array();
$attr['startCount'] = $startCount;
$attr['endCount'] = $endCount;
$attr['commitCount'] = $totalCommitCount;
$text = moduleLiteral::get($moduleID, "lbl_pageInfo_displayText", $attr);
$display = HTML::select(".pgDisplay")->item(0);
DOM::append($display, $text);

// Return output
return $pageContent->getReport($holder);
//#section_end#
?>