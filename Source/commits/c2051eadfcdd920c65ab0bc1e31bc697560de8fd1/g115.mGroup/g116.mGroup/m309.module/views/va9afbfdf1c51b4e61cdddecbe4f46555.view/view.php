<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

// Inner Module Codes
$innerModules = array();
$innerModules['openPage'] = 308;

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
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("DEV", "Version");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;
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

// Check friendly url
if (empty($projectName) && !empty($projectInfo['name']))
{
	$projectName = $projectInfo['name'];
	$url = url::resolve("open", "/projects/".$projectName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$page->build($projectTitle, "openProjectPage", TRUE);

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['openPage'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Check if project is open
if (!$projectInfo['open'])
{
	// Create error notification
	
	// Return output
	return $page->getReport();
}

// Add icon (if any)
$imageBox = HTML::select(".pheader .logoBox .logo")->item(0);
if (isset($projectInfo['icon_url']))
{
	// Add project image
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectInfo['icon_url']);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");
	
	
// Add open graph information
$ogData = array();
$ogData['title'] = $projectTitle;
$ogData['description'] = $projectInfo['description'];
$ogData['type'] = "product";
if (!empty($projectName))
	$projectUrl = url::resolve("open", "/projects/".$projectName);
else
{
	$params = array();
	$params['id'] = $projectID;
	$projectUrl = url::resolve("open", "/projects/project.php", $params);
}
$ogData['url'] = $projectUrl;
$ogData['image'] = $projectIconUrl;
$page->addOpenGraphMeta($ogData);


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

$pDescription = HTML::select(".projectDescription")->item(0);
DOM::innerHTML($pDescription, $projectInfo['description']);


// Get the request button
$requestButton = HTML::select(".pheader .logoBox .request")->item(0);

// Check if member of the project
if ($project->validate())
	HTML::replace($requestButton, NULL);
else
{
	// Set action to button
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($requestButton, $moduleID, "requestInvite", "", $attr, $loading = TRUE);
	
}

// Check if the application has a published release for application center and is online
$appCenterButton = HTML::select(".pheader .logoBox .appcenter")->item(0);
$pVersion = projectLibrary::getLastProjectVersion($projectID);
if (!empty($pVersion) && $projectInfo['online'])
{
	if (empty($projectName))
	{
		$params = array();
		$params["id"] = $projectID;
		$url = url::resolve("apps", "/application.php", $params);
	}
	else
		$url = url::resolve("apps", "/".$projectName."/play");
	DOM::attr($appCenterButton, "href", $url);
}
else
	HTML::replace($appCenterButton, NULL);
	
	
// Create open project action
$createProjectButton = HTML::select(".pheader .logoBox .create")->item(0);
$actionFactory->setModuleAction($createProjectButton, $moduleID, "createProjectAction");

$sections = array();
$sections["readme"] = "readmeMainView";
$sections["repository"] = "repositoryMainView";
$sections["releases"] = "releasesMainView";
$sections["contributors"] = "contributorsMainView";
$sections["issues"] = "issuesMainView";
$sections["reviews"] = "reviewsMainView";
foreach ($sections as $section => $moduleView)
{
	// Set panel target group
	$panel = HTML::select(".panels #".$section)->item(0);
	$page->setNavigationGroup($panel, "navGroup");
	
	// Set navigation item action
	$navItem = HTML::select(".pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Load repository main view
	if (!empty($moduleView))
	{
		$content = module::loadView($moduleID, $moduleView);
		$container = HTML::select(".panels #".$section)->item(0);
		DOM::append($container, $content);
	}
}

// Return output
return $page->getReport();
//#section_end#
?>