<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

// Inner Module Codes
$innerModules = array();
$innerModules['projectDesigner'] = 187;
$innerModules['projectRepository'] = 188;
$innerModules['projectIssues'] = 189;
$innerModules['projectSecurity'] = 207;
$innerModules['projectAnalysis'] = 206;
$innerModules['projectResources'] = 205;
$innerModules['memberManager'] = 211;

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
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \DEV\Projects\project;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectDesignerPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_overviewTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectOverviewPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}


// Project Title, name and Description
$pTitle = HTML::select("h1.projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

$pName = HTML::select(".projectName")->item(0);
if (!empty($projectInfo['name']))
{
	DOM::innerHTML($pName, "(".$projectInfo['name'].")");
	DOM::append($pTitle, $pName);
}
else
	HTML::replace($pName, NULL);
	
$pDesc = HTML::select("h4.projectDescription")->item(0);
DOM::innerHTML($pDesc, $projectInfo['description']);


// Edit Project Information
$title = moduleLiteral::get($moduleID, "lbl_editProject");
$editInfoBtn = HTML::select(".editInfoBtn")->item(0);
DOM::append($editInfoBtn, $title);

// Set edit action
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($editInfoBtn, $moduleID, "projectInfoEditor", "", $attr);

// Publish Project
$title = moduleLiteral::get($moduleID, "lbl_projectPublisherTitle");
$publisherBtn = HTML::select(".projectPublisher")->item(0);
DOM::append($publisherBtn, $title);

// Set edit action
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($publisherBtn, $moduleID, "publishProject", "", $attr);




// Project Designer
$title = moduleLiteral::get($moduleID, "lbl_projectDesignerTitle");
$designerBtn = HTML::select(".designer .title")->item(0);
$url = url::resolve("developer", "/projects/designer.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($designerBtn, $wl);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectDesigner'], "", "", $attr);


// Project Resources
$title = moduleLiteral::get($moduleID, "lbl_projectResourcesTitle");
$repositoryBtn = HTML::select(".resources .title")->item(0);
$url = url::resolve("developer", "/projects/resources.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($repositoryBtn, $wl);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectResources'], "", "", $attr);


// Project Repository
$title = moduleLiteral::get($moduleID, "lbl_projectRepositoryTitle");
$repositoryBtn = HTML::select(".repository .title")->item(0);
$url = url::resolve("developer", "/projects/repository.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($repositoryBtn, $wl);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectRepository'], "", "", $attr);


// Project Analysis
$title = moduleLiteral::get($moduleID, "lbl_projectAnalysisTitle");
$repositoryBtn = HTML::select(".analysis .title")->item(0);
$url = url::resolve("developer", "/projects/analysis.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($repositoryBtn, $wl);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectAnalysis'], "", "", $attr);


// Project Privileges
$title = moduleLiteral::get($moduleID, "lbl_projectSecurityTitle");
$repositoryBtn = HTML::select(".security .title")->item(0);
$url = url::resolve("developer", "/projects/privileges.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");
DOM::append($repositoryBtn, $wl);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectSecurity'], "", "", $attr);


// Project issues
$title = moduleLiteral::get($moduleID, "lbl_issuesTitle");
$header = HTML::select(".projectIssues h3.title")->item(0);
DOM::append($header, $title);

$issuesList = HTML::select(".issuesList")->item(0);
$text = moduleLiteral::get($moduleID, "lbl_projectNoIssues");
$header = DOM::create("h4", $text);
DOM::append($issuesList, $header);

// Go to issues page
$title = moduleLiteral::get($moduleID, "lbl_goToProjectIssues");
$url = url::resolve("developer", "/projects/issues.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$wl = $page->getWeblink($url, $title, "_blank");

$h4 = DOM::create("h4", $wl);
DOM::append($issuesList, $h4);

$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wl, $innerModules['projectIssues'], "", "", $attr);

// Project accounts
$title = moduleLiteral::get($moduleID, "lbl_membersTitle");
$header = HTML::select(".projectMembers h3.title")->item(0);
DOM::append($header, $title);

// Add member
$title = moduleLiteral::get($moduleID, "lbl_manageMembers");
$manageMembersBtn = HTML::select(".projectMembers .manage")->item(0);
DOM::append($manageMembersBtn, $title);

// Set add action
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($manageMembersBtn, $innerModules['memberManager'], "", "", $attr);

$membersList = HTML::select(".memberList")->item(0);
$members = $project->getProjectAccounts();
foreach ($members as $member)
{
	$mRow = DOM::create("div", "", "", "contributor");
	DOM::append($membersList, $mRow);
	
	// Get profile name according to account type
	$profileName = ($member['administrator'] == 1 ? $member['firstname']." ".$member['lastname'] : $member['title']);
	$url = url::resolve("developer", "/profile/index.php");
	$params = array();
	$params['id'] = $member['accountID'];
	$url = url::get($url, $params);
	$wl = $page->getWeblink($url, $profileName, "_blank");
	
	$h4 = DOM::create("h4", $wl, "", "memberName");
	DOM::append($mRow, $h4);
}

// Project Reports
$title = moduleLiteral::get($moduleID, "lbl_reportsTitle");
$header = HTML::select(".projectReports h3.title")->item(0);
DOM::append($header, $title);

// Return output
return $page->getReport();
//#section_end#
?>