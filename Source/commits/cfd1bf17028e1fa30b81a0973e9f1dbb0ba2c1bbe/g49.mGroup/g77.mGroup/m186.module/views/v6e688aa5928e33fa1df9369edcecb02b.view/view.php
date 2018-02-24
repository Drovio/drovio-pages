<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

// Inner Module Codes
$innerModules = array();
$innerModules['overview'] = 225;
$innerModules['resources'] = 205;
$innerModules['repository'] = 188;
$innerModules['analysis'] = 206;
$innerModules['security'] = 207;
$innerModules['tester'] = 212;
$innerModules['issues'] = 229;
$innerModules['members'] = 211;
$innerModules['history'] = on;
$innerModules['localization'] = 252;
$innerModules['settings'] = 254;
$innerModules['statistics'] = 255;
$innerModules['market'] = 256;
$innerModules['publisher'] = 261;
$innerModules['log'] = 244;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
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
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "projectHomePage", TRUE);


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

// Add project status
$projectStatus = HTML::select(".projectStatusContainer .projectStatus")->item(0);
$statusClass = ($projectInfo['online'] ? "online" : "offline");
HTML::addClass($projectStatus, $statusClass);

// Set literal
$statusTitle = HTML::select("h4.statusTitle")->item(0);
$statusLiteral = ($projectInfo['online'] ? "lbl_projectOnline" : "lbl_projectOffline");
$projectStatusTitle = moduleLiteral::get($moduleID, $statusLiteral);
DOM::append($statusTitle, $projectStatusTitle);

// Set publish action
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($projectStatus, $innerModules['publisher'], "", "", $attr);

// Project Designer
$url = url::resolve("developer", "/projects/designer.php");
$params = array();
$params['id'] = $projectID;
$url = url::get($url, $params);
$designerBox = HTML::select(".designer a")->item(0);
DOM::attr($designerBox, "href", $url);

$actions = array();
$actions[] = "overview";
$actions[] = "repository";
$actions[] = "resources";
$actions[] = "tester";
$actions[] = "issues";
$actions[] = "analysis";
$actions[] = "statistics";
$actions[] = "localization";
$actions[] = "security";
$actions[] = "log";
$actions[] = "members";
$actions[] = "settings";
$actions[] = "market";

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