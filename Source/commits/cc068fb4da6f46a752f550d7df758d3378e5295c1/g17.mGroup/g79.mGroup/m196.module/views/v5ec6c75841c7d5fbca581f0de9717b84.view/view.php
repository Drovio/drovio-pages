<?php
//#section#[header]
// Module Declaration
$moduleID = 196;

// Inner Module Codes
$innerModules = array();
$innerModules['overview'] = 198;
$innerModules['pages'] = 219;
$innerModules['source'] = 221;
$innerModules['templates'] = 220;
$innerModules['themes'] = 222;
$innerModules['settings'] = 223;

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
importer::import("ESS", "Protocol");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \DEV\Websites\website;
use \UI\Modules\MPage;
use \SYS\Resources\url;

use \ESS\Protocol\client\NavigatorProtocol;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectHomePage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "websiteDesingerPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

$pName = HTML::select(".projectName")->item(0);
if (!empty($projectInfo['name']))
{
	DOM::innerHTML($pName, "(".$projectInfo['name'].")");
	DOM::append($pTitle, $pName);
}
else
	HTML::replace($pName, NULL);


// Edit Project Information
$editInfoBtn = HTML::select(".editInfoBtn")->item(0);
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($editInfoBtn, $moduleID, "projectInfoEditor", "", $attr);

// Add project status
$projectStatusContainer = HTML::select(".projectStatusContainer")->item(0);
$projectStatusModuleContainer = $page->getModuleContainer($moduleID, $action = "websiteStatus", $attr = array(), $startup = TRUE, $containerID = "projectStatusModuleContainer");
DOM::append($projectStatusContainer, $projectStatusModuleContainer);


$actions = array();
$actions[] = "overview";
$actions[] = "pages";
$actions[] = "source";
$actions[] = "templates";
$actions[] = "themes";
$actions[] = "settings";

// Set sidebar actions
foreach ($actions as $action)
	setSectionAction($moduleID, $actionFactory, $projectID, $action, $innerModules[$action], "");
	
	
// Set selected tab
$selectedTab = empty($_GET['tab']) ? "overview" : $_GET['tab'];
$boxNav = HTML::select(".projectHomePage .prjMenu .".$selectedTab)->item(0);
// Set side navigation selected
HTML::addClass($boxNav, "selected");

// Load content
$content = module::loadView($innerModules[$selectedTab]);
$prjContent = HTML::select(".prjContent")->item(0);
DOM::append($prjContent, $content);

// Return output
return $page->getReport();



function setSectionAction($moduleID, $actionFactory, $projectID, $tab, $actionID, $actionName = "")
{
	if (!isset($actionID))
		return;

	// Set url
	$url = url::resolve("developer", "/projects/project.php");
	$params = array();
	$params['id'] = $projectID;
	$params['tab'] = $tab;
	$url = url::get($url, $params);
	$box = HTML::select(".projectHomePage .prjMenu .".$tab." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($box, $actionID, $actionName, ".prjContent", $attr);
	
	
	// Set static navigation
	$boxNav = HTML::select(".projectHomePage .prjMenu .".$tab)->item(0);
	NavigatorProtocol::staticNav($boxNav, "", "", "", "prjNavItems", $display = "none");
	
	return $box;
}
//#section_end#
?>