<?php
//#section#[header]
// Module Declaration
$moduleID = 225;

// Inner Module Codes
$innerModules = array();
$innerModules['settings'] = 254;
$innerModules['websitePublisher'] = 284;
$innerModules['projectPublisher'] = 261;
$innerModules['iconDialog'] = 381;

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
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Projects\projectReadme;
use \DEV\Version\vcs;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

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

$projectAdmin = accountKey::validateGroup("PROJECT_ADMIN", $projectID, accountKey::PROJECT_KEY_TYPE);


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_overviewTitle", array(), FALSE);
$page->build($ovTitle." | ".$projectTitle, "projectOverviewPage", TRUE);


// Get project status
$prStatus = HTML::select(".projectOverview .projectStatus")->item(0);
$prStatusTitle = HTML::select(".projectOverview .projectStatus .title")->item(0);
switch ($projectInfo['online'])
{
	case 1:
		HTML::addClass($prStatus, "healthy");
		$title = moduleLiteral::get($moduleID, "lbl_projectOnline");
		break;
	case 0:
		HTML::addClass($prStatus, "error");
		$title = moduleLiteral::get($moduleID, "lbl_projectOffline");
		break;
}

// Set project status title
HTML::append($prStatusTitle, $title);


// Project Health
$prHealthTitle = HTML::select(".projectOverview .projectHealth .title")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_projectHealthy");
HTML::append($prHealthTitle, $title);


// Set quick actions
if ($projectAdmin)
{
	// Set action for publisher
	$projectPublisher = HTML::select(".qAction.publish")->item(0);
	$attr = array();
	$attr['id'] = $projectID;
	
	// Choose publisher according to project type
	$projectInfo = $project->info();
	$publisherModuleID = "projectPublisher";
	if ($projectInfo['projectType'] == 5)
		$publisherModuleID = "websitePublisher";
	$actionFactory->setModuleAction($projectPublisher, $innerModules[$publisherModuleID], "", "", $attr);
	
	// Set action for backup
	$projectPublisher = HTML::select(".qAction.backup")->item(0);
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($projectPublisher, $innerModules['settings'], "backupProject", "", $attr);
	
	// Set action for delete
	$projectPublisher = HTML::select(".qAction.delete")->item(0);
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($projectPublisher, $innerModules['settings'], "deleteProject", "", $attr);
	
	
	
	// Check completed tasks
	$completedTasks = 0;
	$task_icon = HTML::select(".complete_tasks .rtask.icon")->item(0);
	$projectIconUrl = $project->getIconUrl();
	if (isset($projectIconUrl))
	{
		HTML::addClass($task_icon, "done");
		$completedTasks++;
	}
	
	// Set action to update the icon
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($task_icon, $innerModules['iconDialog'], "", "", $attr);
	
	$task_readme = HTML::select(".complete_tasks .rtask.readme")->item(0);
	$projectReadme = new projectReadme($project->getRootFolder(), TRUE);
	$readmeContent = $projectReadme->load();
	if (!empty($readmeContent))
	{
		HTML::addClass($task_readme, "done");
		$completedTasks++;
	}
	
	$task_pname = HTML::select(".complete_tasks .rtask.pname")->item(0);
	if (!empty($projectInfo['name']))
	{
		HTML::addClass($task_pname, "done");
		$completedTasks++;
	}
	
	if ($completedTasks == 3)
	{
		$completeTasks = HTML::select(".complete_tasks")->item(0);
		HTML::replace($completeTasks, NULL);
		
		$projectOverview = HTML::select(".projectOverview")->item(0);
		HTML::removeClass($projectOverview, "with_tasks");
	}
	else
	{
		$progress = number_format(($completedTasks/3)*100, 0);
		
		// Set completed into header
		$header = HTML::select(".complete_tasks .header")->item(0);
		$attr = array();
		$attr['progress'] = $progress;
		$title = moduleLiteral::get($moduleID, "lbl_completeProgress", $attr);
		HTML::append($header, $title);
		
		// Set progress bar
		$progressBar = HTML::select(".complete_tasks .progress_bar")->item(0);
		HTML::attr($progressBar, "style", "width: ".$progress."%");
	}
}
else
{
	// Remove quick actions
	$qActions = HTML::select(".qAction");
	foreach ($qActions as $qAction)
		HTML::replace($qAction, NULL);
	
	// Remove completed tasks
	$ctasks = HTML::select(".complete_tasks")->item(0);
	HTML::replace($ctasks, NULL);
	
	$projectOverview = HTML::select(".projectOverview")->item(0);
	HTML::removeClass($projectOverview, "with_tasks");
}



// Fill stats

// Project ID
$projectIDHolder = HTML::select(".stats .sr.projectId .value")->item(0);
HTML::innerHTML($projectIDHolder, $projectID);

$releases = $project->getReleases();

// Project Last Release Version
$lastRelease = $releases[0];
if (empty($lastRelease))
	$projectVersion = " - ";
else
	$projectVersion = $lastRelease['version'];
$projectVersionHolder = HTML::select(".stats .sr.projectVersion .value")->item(0);
HTML::innerHTML($projectVersionHolder, $projectVersion);

// Project Last Release Date
if (empty($lastRelease))
	$date = DOM::create("span", " - ");
else
	$date = datetimer::live($lastRelease['time_created']);
$projectDateHolder = HTML::select(".stats .sr.projectReleaseDate .value")->item(0);
HTML::append($projectDateHolder, $date);

// Source Stats

// Head Branch
$vcs = new vcs($projectID);
$headBranch = $vcs->getHeadBranch();
$projectHeadBranch = HTML::select(".stats .sr.headBranch .value")->item(0);
HTML::innerHTML($projectHeadBranch, $headBranch);

// Head branch version
$branchReleases = $vcs->getReleases();
$headReleases = $branchReleases[$headBranch];
if (empty($headReleases['packages']))
	$branchVersion = " - ";
else
{
	$currentVersion = $headReleases['current'];
	$headVersion = $headReleases['packages']["v".$currentVersion]['version'];
	$headBuild = $headReleases['packages']["v".$currentVersion]['build'];
	$branchVersion = $headVersion."-".$headBuild;
}
$projectHeadVersion = HTML::select(".stats .sr.branchVersion .value")->item(0);
HTML::innerHTML($projectHeadVersion, $branchVersion);

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>