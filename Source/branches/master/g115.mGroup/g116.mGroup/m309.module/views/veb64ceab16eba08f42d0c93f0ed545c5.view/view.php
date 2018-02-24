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
importer::import("API", "Literals");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "projectContributors");

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

// Get vcs authors
$vcs = new vcs($projectID);
$authors = $vcs->getAuthors();

// Get project members
$projectAccounts = $project->getProjectAccounts();
foreach ($projectAccounts as $account)
{
	$authDesc = moduleLiteral::get($moduleID, "lbl_activeMember", array(), FALSE);
	$authorViewer = getContributorView($pageContent, $account['accountID'], $account['title'], $account['username'], $authDesc);
	$pageContent->append($authorViewer);
	
	// Unset from authors
	unset($authors[$account['accountID']]);
}

foreach ($authors as $authorID => $authorName)
{
	$authDesc = moduleLiteral::get($moduleID, "lbl_pastContributor", array(), FALSE);
	$authorViewer = getContributorView($pageContent, $authorID, $authorName, "", $authDesc);
	$pageContent->append($authorViewer);
}


return $pageContent->getReport();

function getContributorView($pageContent, $authorID, $authorName, $authorUsername, $authDesc)
{
	// Create header
	$vHeader = DOM::create("div", "", "", "cHeader");
	
	// Set header image
	$initials = getInitials($authorName);
	$img = DOM::create("div", $initials, "", "cImg");
	DOM::append($vHeader, $img);
	
	// Contributor Name
	$cInfo = DOM::create("div", "", "", "cInfo");
	DOM::append($vHeader, $cInfo);
	
	$author = DOM::create("h3", $authorName, "", "cName");
	DOM::append($cInfo, $author);
	
	// Subtitle
	$subtitle = DOM::create("h4", $authDesc, "", "cDesc");
	DOM::append($cInfo, $subtitle);
	
	// Create and return author viewer weblink
	if (empty($authorUsername))
	{
		$params = array();
		$params['id'] = $authorID;
		$href = url::resolve("developer", "/profile/index.php", $params);
	}
	else
		$href = url::resolve("developer", "/profile/".$authorUsername);
	$authorViewer = $pageContent->getWeblink($href, $vHeader, "_blank");
	HTML::addClass($authorViewer, "cViewer");
	return $authorViewer;
}

function getInitials($author)
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