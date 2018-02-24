<?php
//#section#[header]
// Module Declaration
$moduleID = 358;

// Inner Module Codes
$innerModules = array();
$innerModules['applicationPlayer'] = 169;
$innerModules['appCenter'] = 92;

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
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Model\apps\application;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Projects\projectLibrary;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$applicationID = engine::getVar('id');
$applicationName = engine::getVar('name');

// Get application info from project
$project = new project($applicationID, $applicationName);
$projectInfo = $project->info();

// Check friendly url
if (empty($applicationName) && !empty($projectInfo['name']))
{
	$url = url::resolve("apps", "/".$projectInfo['name']);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get project data
$applicationID = $projectInfo['id'];
$applicationName = $projectInfo['name'];

// Get application version information for the last version
$applicationInfo = application::getApplicationInfo($applicationID);
$applicationTitle = $applicationInfo['title'];

// Build module page
$page->build($applicationTitle, "applicationInfoViewer", TRUE);

// Check if project is application and has a valid version
if (!$projectInfo['online'] || $projectInfo['projectType'] != 4 || empty($applicationInfo))
{
	// Redirect to open project's home page
	$url = url::resolve("apps", "/");
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Add icon (if any)
$imageBox = HTML::select(".pheader .logoBox .logo")->item(0);
if (isset($applicationInfo['icon_url']))
{
	// Add project image
	$img = DOM::create("img");
	DOM::attr($img, "src", $applicationInfo['icon_url']);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");
	
	
// Add open graph information
$ogData = array();
$ogData['title'] = $applicationTitle;
$ogData['description'] = $applicationInfo['description'];
$ogData['type'] = "product";
if (!empty($applicationName))
	$applicationUrl = url::resolve("apps", "/".$applicationName);
else
{
	$params = array();
	$params['id'] = $applicationID;
	$applicationUrl = url::resolve("apps", "/application.php", $params);
}
$ogData['url'] = $applicationUrl;
$ogData['image'] = $applicationInfo['icon_url'];
$page->addOpenGraphMeta($ogData);


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $applicationTitle);

$pDescription = HTML::select(".projectDescription")->item(0);
DOM::innerHTML($pDescription, $applicationInfo['description']);


// Get the play button
$playButton = HTML::select(".pheader .logoBox .rbutton.play")->item(0);

// Add href attribute
if (!empty($applicationName))
	$playUrl = url::resolve("apps", "/".$applicationName."/play");
else
{
	$params = array();
	$params['id'] = $applicationID;
	$playUrl = url::resolve("apps", "/player.php", $params);
}
DOM::attr($playButton, "href", $playUrl);

// Set action to button
$attr = array();
$attr['id'] = $applicationID;
$attr['name'] = $applicationName;
$actionFactory->setModuleAction($playButton, $innerModules['applicationPlayer'], "playApp", ".appCenterContentHolder", $attr, $loading = TRUE);

$sections = array();
$sections["about"] = "aboutMainView";
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

// Load footer menu
$applicationInfo = HTML::select(".applicationInfo")->item(0);
$footerMenu = module::loadView($innerModules['appCenter'], "footerMenu");
DOM::append($applicationInfo, $footerMenu);

// Add action to show back button in navigation bar
$page->addReportAction("appcenter.navigation.showhide_back", 1);

// Add app info href
if (!empty($applicationName))
	$appInfoUrl = url::resolve("apps", "/".$applicationName);
else
{
	$params = array();
	$params['id'] = $applicationID;
	$appInfoUrl = url::resolve("apps", "/application.php", $params);
}
$page->addReportAction("appcenter.navigation.appinfo_href", $appInfoUrl);

// Return output
return $page->getReport();
//#section_end#
?>