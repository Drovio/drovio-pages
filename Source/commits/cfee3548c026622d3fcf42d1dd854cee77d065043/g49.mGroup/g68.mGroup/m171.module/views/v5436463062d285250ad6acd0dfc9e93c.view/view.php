<?php
//#section#[header]
// Module Declaration
$moduleID = 171;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Profile");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\appManager;
use \API\Developer\appcenter\application;
use \API\Developer\misc\vcs;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Html\HTMLContent;
use \UI\Presentation\togglers\toggler;

// Create Module Page
$pageContent = new HTMLContent();

// Get application id
$appID = $_GET['appID'];

if (empty($appID))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}

// Validate and Load application info
$application = appManager::getApplicationData($appID);
if (is_null($application))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}

// Init application
$devApp = new application($appID);
$vcs = $devApp->getVCS();

// Build the module
$pageContent->build("", "vcsSectionContent");

// Get authors
$authors = $vcs->getAuthors();

// Get branch commits
$commits = $vcs->getBranchCommits();
$commits = array_reverse($commits, TRUE);

// Total commits
if (count($commits) > 0)
{
	$totalCommits = DOM::create("p", "There is a total of ".count($commits)." commits in this branch.");
	$pageContent->append($totalCommits);
}

foreach ($commits as $commitID => $commitData)
{
	// Create toggler
	$tog = new toggler();
	
	// Set toggler data
	$commitDate = date("M j\, Y \a\\t H:i", $commitData['time']);
	$togHeader = DOM::create("span", "[".$commitDate."] - ");
	$cnt = DOM::create("span", $commitData['description']." - by ");
	DOM::append($togHeader, $cnt);
	$cnt = DOM::create("b", $authors[$commitData['author']]);
	DOM::append($togHeader, $cnt);
	$togBody = DOM::create("div", "", "", "commitInfo");
	
	// Header
	$header = DOM::create("div", "", "", "infoHeader");
	DOM::append($togBody, $header);
	
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