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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Websites\website;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

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
$prStatus = HTML::select(".wsOverview .status .boxStatus.project")->item(0);
$prStatusTitle = HTML::select(".wsOverview .status .boxStatus.project .title")->item(0);
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
$srvStatus = HTML::select(".wsOverview .status .boxStatus.server")->item(0);
$srvStatusTitle = HTML::select(".wsOverview .status .boxStatus.server .title")->item(0);
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
$wsPublisher = HTML::select(".wsOverview .status .boxStatus.project .configure")->item(0);
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($wsPublisher, $innerModules['publisher'], "", "", $attr);


// Return output
return $page->getReport($_GET['holder'], FALSE);
//#section_end#
?>