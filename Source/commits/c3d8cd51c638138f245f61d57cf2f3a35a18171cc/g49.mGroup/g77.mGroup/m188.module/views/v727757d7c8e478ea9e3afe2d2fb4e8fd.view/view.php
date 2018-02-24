<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

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
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "vcsCommitsContainer", TRUE);

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
	$commitGroup = getCommitGroup($groupDate, $commitListData, $vcs, $moduleID);
	DOM::append($commitsContainer, $commitGroup);
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
	$attr['id'] = $projectID;
	$attr['bn'] = $branchName;
	$attr['page'] = $currentPage - 1;
	$actionFactory->setModuleAction($newerBtn, $moduleID, "repositoryCommits", "#commitsContainer", $attr);
}

if (($currentPage + 1) * $pageNumCommits < $totalCommitCount)
{
	HTML::addClass($olderBtn, "active");
	$attr = array();
	$attr['id'] = $projectID;
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


function getCommitGroup($groupDate, $commitListData, $vcs, $moduleID)
{
	// Create group
	$commitGroupContainer = DOM::create("div", "", "", "commitGroupContainer");
	
	// Create commitGroup header
	foreach ($commitListData as $commitID => $commitData)
	{
		$firstCommit = $commitData;
		break;
	}
	$attr = array();
	$attr['date'] = date("F d, Y", $firstCommit['time']);
	$header = moduleLiteral::get($moduleID, "hd_commitGroup", $attr);
	$commitGroupHeader = DOM::create("div", $header, "", "commitGroupHeader");
	DOM::append($commitGroupContainer, $commitGroupHeader);
	
	// Create commitList body
	$commitList = DOM::create("div", "", "", "commitList");
	DOM::append($commitGroupContainer, $commitList);
	
	// Insert all commits
	foreach ($commitListData as $commitID => $commitData)
	{
		$commitViewer = getCommitViewer($commitID, $commitData, $vcs);
		DOM::append($commitList, $commitViewer);
	}
	
	// Return container
	return $commitGroupContainer;
}

function getCommitViewer($commitID, $commitData, $vcs)
{
	// Create commit viewer
	$commitViewer = DOM::create("div", "", "", "cViewer");
	
	// Create header
	$vHeader = DOM::create("div", "", "", "cvHeader");
	DOM::append($commitViewer, $vHeader);
	
	// Set header image
	$initials = getAuthorInitials($commitData['author']);
	$img = DOM::create("div", $initials, "", "authImg");
	DOM::append($vHeader, $img);
	
	// Info
	$cInfo = DOM::create("div", "", "", "cInfo");
	DOM::append($vHeader, $cInfo);
	
	// Commit summary
	$summary = DOM::create("h3", $commitData['summary'], "", "cvsum");
	DOM::append($cInfo, $summary);
	
	// Commit description
	$author = DOM::create("div", $commitData['author'].", ", "", "cvAuthor");
	DOM::append($cInfo, $author);
	$commitDate = DOM::create("span", "commited ", "", "cDate");
	DOM::append($author, $commitDate);
	$liveDate = datetimer::live($commitData['time']);
	DOM::append($commitDate, $liveDate);
	
	// Id
	$cID = DOM::create("div", substr($commitID, 0, 10), "", "cID");
	DOM::append($vHeader, $cID);
	
	// Tags
	$tags = $commitData['tags'];
	if (!empty($tags))
	{
		$cTagContainer = DOM::create("div", "", "", "cTagContainer");
		DOM::append($vHeader, $cTagContainer);
		
		$tagList = explode(",", $tags);
		foreach ($tagList as $key => $tag)
			$tagList[$key] = trim($tag);
		asort($tagList);
		foreach ($tagList as $tag)
		{
			$cTag = DOM::create("span", trim($tag), "", "cTag");
			DOM::append($cTagContainer, $cTag);
		}
	}
	
	
	// Commit details
	$cDetails = DOM::create("div", "", "", "cDetails");
	DOM::append($commitViewer, $cDetails);

	
	// Commit ID
	$cidContext = DOM::create("div", $commitID, "", "crow cid");
	DOM::append($cDetails, $cidContext);
	
	// Commit Summary
	$cDesc = DOM::create("div", $commitData['summary'], "", "crow");
	DOM::append($cDetails, $cDesc);
	
	// Commit Description
	if (!empty($commitData['description']))
	{
		$cDesc = DOM::create("div", $commitData['description'], "", "crow cDesc");
		DOM::append($cDetails, $cDesc);
	}
	
	// Item List
	$itemList = DOM::create("ul", "", "", "commitItems");
	DOM::append($cDetails, $itemList);
	
	$commitItems = $vcs->getCommitItems($commitID);
	foreach ($commitItems as $itemID => $itemInfo)
	{
		$li = DOM::create("li", "", "", "citem");
		DOM::append($itemList, $li);
		
		// Item path
		$path = DOM::create("span", directory::normalize("/".$itemInfo['path']."/".$itemInfo['name']), "", "ipath");
		DOM::append($li, $path);
		
		// Item id
		$iid = DOM::create("span", $itemID, "", "iid");
		DOM::append($li, $iid);
	}
	
	// Return commit viewer
	return $commitViewer;
}

function getAuthorInitials($author)
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