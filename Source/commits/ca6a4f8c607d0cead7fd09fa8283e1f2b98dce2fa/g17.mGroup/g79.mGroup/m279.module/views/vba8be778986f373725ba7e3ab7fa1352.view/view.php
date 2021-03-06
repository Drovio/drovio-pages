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
importer::import("DEV", "Websites");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Websites\website;

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


// Build module page
$page->build($projectTitle, "websiteOverviewPage", TRUE);


// Get project status
$prStatus = HTML::select(".websiteOverview .boxStatus.project")->item(0);
$prStatusTitle = HTML::select(".websiteOverview .boxStatus.project .title")->item(0);
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
$srvStatus = HTML::select(".websiteOverview .boxStatus.server")->item(0);
$srvStatusTitle = HTML::select(".websiteOverview .boxStatus.server .title")->item(0);
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


// Website publisher action
$wsPublisher = HTML::select(".websiteOverview .wbutton.publish")->item(0);
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wsPublisher, $innerModules['publisher'], "", "", $attr);


// Return output
return $page->getReport($_GET['holder'], FALSE);
//#section_end#
?>