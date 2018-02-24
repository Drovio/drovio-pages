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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Developer\appcenter\appManager;
use \API\Developer\appcenter\application;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage("freeLayout");
$actionFactory = $page->getActionFactory();

// Get application id
$appID = $_GET['id'];

if (empty($appID))
{
	// Application id doesn't exist, return to home page
	return $actionFactory->getReportRedirect("/apps/", "developer");
}

// Open Application
appManager::openApplication($appID);

// Validate and Load application info
$application = appManager::getApplicationData($appID);
if (is_null($application))
{
	// Close Application
	appManager::closeApplication($appID);
	
	// Application doesn't exist, return to home page
	return $actionFactory->getReportRedirect("/apps/", "developer");
}

// Build the module
$title = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($title, "appVCSViewer");

// Check if its a public repository or account is author
if ($application['scope'] == "private" && $application['authorID'] != account::getAccountID())
{
	// Close Application
	appManager::closeApplication($appID);
	
	// Error Message
	$errorMessage = DOM::create("h2", "This is a private repository.");
	$page->appendToSection("main", $errorMessage);
	return $page->getReport();
}


// Header
$headerContent = DOM::create("div", "", "", "headBar");
$page->appendToSection("main", $headerContent);

// Developer's Profile
$devProfile = DOM::create("div", "", "", "devProfile");
DOM::append($headerContent, $devProfile);

$author = DOM::create("h4", account::getFirstname()." ".account::getLastname());
DOM::append($devProfile, $author);

// Header Title
$title = moduleLiteral::get($moduleID, "lbl_vcsTitle");
$header = DOM::create("h2", $title, "", "headTitle");
$betaContent = DOM::create("span", "BETA", "", "beta");
DOM::append($header, $betaContent);
DOM::append($headerContent, $header);


// Global Container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$page->appendToSection("main", $globalContainer);


// Application Title
$appName = $application['fullName'];
$appNameTitle = DOM::create("h3", $appName);
DOM::append($globalContainer, $appNameTitle);

// Navigation Bar
$navBar = DOM::create("div", "", "", "navBar");
DOM::append($globalContainer, $navBar);

// Navigation attributes
$targetcontainer = "vcsSections";
$targetgroup = "vcsNavGroup";
$navgroup = "vcsNav";

// Overview
$navTitle = DOM::create("div", "Overview", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "vcsOverview", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);
DOM::appendAttr($navTitle, "class", "selected");

// Commits
$navTitle = DOM::create("div", "Commits", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "vcsCommits", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);

// Branches
$navTitle = DOM::create("div", "Branches", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "vcsBranches", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);

// Releases
$navTitle = DOM::create("div", "Releases", "", "navTitle");
NavigatorProtocol::staticNav($navTitle, "vcsReleases", $targetcontainer, $targetgroup, $navgroup, $display = "none");
DOM::append($navBar, $navTitle);


$sectionsContainer = DOM::create("div", "", "vcsSections");
DOM::append($globalContainer, $sectionsContainer);

$attr = array();
$attr['appID'] = $appID;

// Overview Container
$navContainer = $page->getNavigationGroup("vcsOverview", $targetgroup);
DOM::append($sectionsContainer, $navContainer);
$navContent = $page->getModuleContainer($moduleID, $action = "overViewer", $attr, $startup = TRUE, $containerID = "authorViewer");
DOM::append($navContainer, $navContent);

// Commits Container
$navContainer = $page->getNavigationGroup("vcsCommits", $targetgroup);
DOM::append($sectionsContainer, $navContainer);
$navContent = $page->getModuleContainer($moduleID, $action = "commitsViewer", $attr, $startup = TRUE, $containerID = "commitsViewer");
DOM::append($navContainer, $navContent);

// Branches Container
$navContainer = $page->getNavigationGroup("vcsBranches", $targetgroup);
DOM::append($sectionsContainer, $navContainer);
$navContent = $page->getModuleContainer($moduleID, $action = "branchesViewer", $attr, $startup = TRUE, $containerID = "branchesViewer");
DOM::append($navContainer, $navContent);

// Releases Container
$navContainer = $page->getNavigationGroup("vcsReleases", $targetgroup);
DOM::append($sectionsContainer, $navContainer);
$navContent = $page->getModuleContainer($moduleID, $action = "releaseViewer", $attr, $startup = TRUE, $containerID = "releaseViewer");
DOM::append($navContainer, $navContent);

// Return output
return $page->getReport();
//#section_end#
?>