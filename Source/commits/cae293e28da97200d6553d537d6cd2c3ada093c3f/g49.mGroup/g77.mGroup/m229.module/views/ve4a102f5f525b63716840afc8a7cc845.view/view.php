<?php
//#section#[header]
// Module Declaration
$moduleID = 229;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \DEV\BugTracker\bugTracker;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (empty($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectIssuesPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($projectTitle." | ".$title, "projectIssuesPage issuesView", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}

//
$right = HTML::select('.mContent > .cRight')->item(0); 
$addNew = HTML::select('.mContent > .cRight .reportNew > span')->item(0); 
$actionFactory->setModuleAction($addNew, $moduleID, "newBug", "", $attr = array("pid" => $projectID));

//
//$left = HTML::select('.mContent > .cMiddle')->item(0); //DOM::create('div');
// add menu events

$issueList = HTML::select('.mContent .cMiddle .bugList')->item(0);
	$list = module::loadview($moduleID, "bugList");
	DOM::append($issueList, $list);




return $page->getReport();
//#section_end#
?>