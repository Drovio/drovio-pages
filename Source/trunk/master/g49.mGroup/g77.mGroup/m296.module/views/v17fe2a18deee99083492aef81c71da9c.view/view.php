<?php
//#section#[header]
// Module Declaration
$moduleID = 296;

// Inner Module Codes
$innerModules = array();
$innerModules['appTesterPreview'] = 137;

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
importer::import("UI", "Presentation");
importer::import("UI", "Core");
importer::import("UI", "Modules");
importer::import("ESS", "Environment");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \ESS\Environment\session;
use \UI\Core\RCPageReport;
use \UI\Modules\MPage;
use \UI\Presentation\notification;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Validate domain
$urlInfo = url::info();
if (!empty($urlInfo['referer']))
	$urlInfo = url::info($urlInfo['referer']);
$subdomain = $urlInfo['sub'];
$domain = $urlInfo['domain'];
$invalidSubdomain = !($domain == "redback.gr" && $subdomain == "developer");

// Validate token
$token = $_GET['token'];
$sessionToken = session::get("tester_project_".$projectID, $default = NULL, $namespace = 'developer_tester');
$invalidToken = (empty($token) || ($token != $sessionToken));

// Check validation and show notification on error
if ($invalidToken || $invalidSubdomain)
{
	// Build the report
	$report = new RCPageReport();
	return $report->build("error", "error", "err.invalid_access")->getReport();
}


// Build module page
$page->build($projectTitle, "projectTesterPreviewFrame", TRUE);


// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 4:
		// Application Tester Page
		$testerModuleID = $innerModules['appTesterPreview'];
		break;
	case 5:
		// Website Tester Page
		$testerModuleID = $innerModules['websiteTesterPreview'];
		break;
	case 6:
		// Redback Website Template Tester
		$testerModuleID = $innerModules['webTemplateTesterPreview'];
		break;
	case 7:
		// Redback Website Extension Tester
		$testerModuleID = $innerModules['webExtensionTesterPreview'];
		break;
}


// Create module container
$attr = array();
$attr['projectID'] = $projectID;
$testerPreview = $page->getModuleContainer($testerModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectTesterPreview");

// Append to uiMainContent
$uiMainContent = HTML::select(".projectTesterPreviewFrame .uiMainContent")->item(0);
DOM::append($uiMainContent, $testerPreview);

// Return output
return $page->getReport();
//#section_end#
?>