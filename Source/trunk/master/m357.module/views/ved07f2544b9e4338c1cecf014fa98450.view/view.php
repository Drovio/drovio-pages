<?php
//#section#[header]
// Module Declaration
$moduleID = 357;

// Inner Module Codes
$innerModules = array();
$innerModules['developerHome'] = 100;

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
	$url = url::resolve("developers", "/public/".$projectName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$page->build($projectTitle, "publicProjectPage", TRUE);

// Check if project is open
if (!$projectInfo['public'] && !$projectInfo['open'])
{
	// Redirect to open project's home page
	$url = url::resolve("developers", "/");
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Add icon (if any)
$imageBox = HTML::select(".publicProject .appIcon")->item(0);
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
	$projectUrl = url::resolve("developers", "/public/".$projectName);
else
{
	$params = array();
	$params['id'] = $projectID;
	$projectUrl = url::resolve("developers", "/public/index.php", $params);
}
$ogData['url'] = $projectUrl;
$ogData['image'] = $projectIconUrl;
$page->addOpenGraphMeta($ogData);


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

// Check if the application has a published release for application center and is online
$appCenterButton = HTML::select(".publicProject .wbutton.appcenter")->item(0);
$pVersion = projectLibrary::getLastProjectVersion($projectID);
if (!empty($pVersion) && $projectInfo['online'] && $projectInfo['projectType'] == 4)
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


$sections = array();
$sections["readme"] = "readmeMainView";
$sections["reviews"] = "reviewsMainView";
foreach ($sections as $section => $moduleView)
{
	// Set navigation item action
	$navItem = HTML::select(".pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Load repository main view
	if (!empty($moduleView))
	{
		$attr = array();
		$attr['id'] = $applicationID;
		$attr['name'] = $applicationName;
		$mContainer = $page->getModuleContainer($moduleID, $moduleView, $attr, $startup = TRUE, $containerID = $section, $loading = FALSE, $preload = TRUE);
		DOM::append($container, $mContainer);
		$page->setNavigationGroup($mContainer, "navGroup");
	}
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$openProjectContainer = HTML::select(".publicProject")->item(0);
$footerMenu = module::loadView($innerModules['developerHome'], "footerMenu");
DOM::append($openProjectContainer, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>