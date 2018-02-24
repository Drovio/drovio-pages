<?php
//#section#[header]
// Module Declaration
$moduleID = 279;

// Inner Module Codes
$innerModules = array();
$innerModules['publisher'] = 284;

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
importer::import("API", "Security");
importer::import("DEV", "Websites");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Websites\website;
use \DEV\Websites\wsServer;
use \DEV\Websites\settings\wsSettings;
use \DEV\Websites\settings\metaSettings;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

$projectAdmin = accountKey::validateGroup("PROJECT_ADMIN", $projectID, accountKey::PROJECT_KEY_TYPE);

// Build module page
$page->build($projectTitle, "websiteOverviewPage", TRUE);


// Get project status
$prStatus = HTML::select(".websiteOverview .box.projectStatus")->item(0);
$prStatusTitle = HTML::select(".websiteOverview .box.projectStatus .title")->item(0);
switch ($projectInfo['online'])
{
	case 1:
		HTML::addClass($prStatus, "healthy");
		$title = moduleLiteral::get($moduleID, "lbl_wsOnline");
		break;
	case 0:
		HTML::addClass($prStatus, "error");
		$title = moduleLiteral::get($moduleID, "lbl_wsOffline");
		break;
}

// Set project status title
HTML::append($prStatusTitle, $title);


// Check server status
$serverStatus = 0;
$srvStatus = HTML::select(".websiteOverview .box.projectHealth")->item(0);
$srvStatusTitle = HTML::select(".websiteOverview .box.projectHealth .title")->item(0);
switch ($serverStatus)
{
	case 2:
		HTML::addClass($srvStatus, "healthy");
		$title = moduleLiteral::get($moduleID, "lbl_serverHealthy");
		break;
	case 1:
		HTML::addClass($srvStatus, "error");
		$title = moduleLiteral::get($moduleID, "lbl_serverError");
		break;
	case 0:
		HTML::addClass($srvStatus, "warning");
		$title = moduleLiteral::get($moduleID, "lbl_serverUnconfigured");
		break;
}

// Set project status title
HTML::append($srvStatusTitle, $title);

// Set quick actions
if ($projectAdmin)
{
	// Set action for publisher
	$wsPublisher = HTML::select(".qAction.publish")->item(0);
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($wsPublisher, $innerModules['publisher'], "", "", $attr);
	
	
	
	// Check completed tasks
	$completedTasks = 0;
	$task_server = HTML::select(".complete_tasks .rtask.server")->item(0);
	$wsServer = new wsServer($projectID);
	$servers = $wsServer->getServerList();
	if (!empty($servers))
	{
		HTML::addClass($task_server, "done");
		$completedTasks++;
	}
	
	$task_url = HTML::select(".complete_tasks .rtask.url")->item(0);
	$wsSettings = new wsSettings($projectID);
	$site_url = $wsSettings->get('site_url');
	if (!empty($site_url))
	{
		HTML::addClass($task_url, "done");
		$completedTasks++;
	}
	
	$task_meta = HTML::select(".complete_tasks .rtask.meta")->item(0);
	$metaSettings = new metaSettings($websiteID);
	$meta_desc = $metaSettings->get('meta_description');
	$meta_keys = $metaSettings->get('meta_keywords');
	if (!empty($meta_desc) && !empty($meta_keys))
	{
		HTML::addClass($task_meta, "done");
		$completedTasks++;
	}
	
	if ($completedTasks == 3)
	{
		$completeTasks = HTML::select(".complete_tasks")->item(0);
		HTML::replace($completeTasks, NULL);
		
		$projectOverview = HTML::select(".websiteOverview")->item(0);
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


// Return output
return $page->getReport($_GET['holder'], FALSE);
//#section_end#
?>