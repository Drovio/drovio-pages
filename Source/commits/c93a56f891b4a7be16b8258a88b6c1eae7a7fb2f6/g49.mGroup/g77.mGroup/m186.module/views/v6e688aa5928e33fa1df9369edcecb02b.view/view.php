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
$innerModules['localization'] = 252;
$innerModules['settings'] = 254;
$innerModules['statistics'] = 255;
$innerModules['market'] = 256;
$innerModules['log'] = 318;
$innerModules['releases'] = 244;
$innerModules['release_log'] = on;
$innerModules['dev_log'] = on;
$innerModules['publisher'] = 261;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "projectHomePage", TRUE);


// Add icon (if any)
$imageBox = HTML::select(".projectHome .prjHmHeader .projectImage")->item(0);
$projectIcon = $project->getResourcesFolder()."/.assets/icon.png";
if (file_exists(systemRoot.$projectIcon))
{
	// Resolve path
	$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
	$projectIcon = url::resolve("repo", $projectIcon);
	
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectIcon);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

$pName = HTML::select(".projectName")->item(0);
if (!empty($projectName))
{
	DOM::innerHTML($pName, "(".$projectName.")");
	DOM::append($pTitle, $pName);
}
else
	HTML::replace($pName, NULL);

// Add project status
$projectStatus = HTML::select(".projectStatusContainer .projectStatus")->item(0);
$statusClass = ($projectInfo['online'] ? "online" : "offline");
HTML::addClass($projectStatus, $statusClass);

// Add status updater
if (accountKey::validateGroup("PROJECT_ADMIN", $projectID, accountKey::PROJECT_KEY_TYPE))
{
	// Load the status switcher
	$projectStatusContainer = HTML::select(".projectStatusContainer")->item(0);
	$statusSwitcher = module::loadView($innerModules['publisher'], "setProjectStatus");
	HTML::append($projectStatusContainer, $statusSwitcher);
}

// Project Designer
if (empty($projectName))
{
	$url = url::resolve("developer", "/projects/designer.php");
	$params = array();
	$params['id'] = $projectID;
	$url = url::get($url, $params);
}
else
	$url = url::resolve("developer", "/projects/".$projectName."/designer/");
$designerBox = HTML::select(".designer a")->item(0);
DOM::attr($designerBox, "href", $url);

$actions = array();
$actions[] = "overview";
$actions[] = "repository";
$actions[] = "resources";
$actions[] = "tester";
$actions[] = "issues";
$actions[] = "log";
$actions[] = "analysis";
$actions[] = "statistics";
$actions[] = "localization";
$actions[] = "security";
$actions[] = "releases";
$actions[] = "members";
$actions[] = "settings";
$actions[] = "market";

// Set sidebar actions
foreach ($actions as $action)
	setSectionAction($moduleID, $actionFactory, $projectID, $action, $innerModules[$action], "", $projectName);
	
	
// Set selected tab
$selectedTab = engine::getVar('tab');
$selectedTab = empty($selectedTab) ? "overview" : $selectedTab;
$boxNav = HTML::select(".projectHomePage .prjMenu .".$selectedTab)->item(0);
// Set side navigation selected
HTML::addClass($boxNav, "selected");

// Load content
$content = module::loadView($innerModules[$selectedTab]);
$prjContent = HTML::select(".prjContent")->item(0);
DOM::append($prjContent, $content);

// Return output
return $page->getReport();


// Set side navigation attributes and actions
function setSectionAction($moduleID, $actionFactory, $projectID, $tab, $actionID, $actionName = "", $projectName = "")
{
	if (!isset($actionID))
		return;

	// Set url
	if (empty($projectName))
	{
		$url = url::resolve("developer", "/projects/project.php");
		$params = array();
		$params['id'] = $projectID;
		$params['tab'] = $tab;
		$url = url::get($url, $params);
	}
	else
		$url = url::resolve("developer", "/projects/".$projectName."/".$tab."/");
	$box = HTML::select(".projectHomePage .prjMenu .".$tab." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['name'] = $projectName;
	$actionFactory->setModuleAction($box, $actionID, $actionName, ".prjContent", $attr);
	
	
	// Set static navigation
	$boxNav = HTML::select(".projectHomePage .prjMenu .".$tab)->item(0);
	NavigatorProtocol::staticNav($boxNav, "", "", "", "prjNavItems", $display = "none");
	
	return $box;
}
//#section_end#
?>