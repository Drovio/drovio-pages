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
$imageBox = HTML::select(".applicationInfo .appIcon")->item(0);
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
	$applicationInfoUrl = url::resolve("apps", "/".$applicationName);
else
{
	$params = array();
	$params['id'] = $applicationID;
	$applicationInfoUrl = url::resolve("apps", "/application.php", $params);
}
$ogData['url'] = $applicationInfoUrl;
$ogData['image'] = $applicationInfo['icon_url'];
$page->addOpenGraphMeta($ogData);


// Project Title, name and Description
$pTitle = HTML::select(".appTitle")->item(0);
DOM::innerHTML($pTitle, $applicationTitle);


// Get the play button
$playButton = HTML::select(".applicationInfo .navitem.play")->item(0);

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
$sections["about"] = "appInfo";
$sections["reviews"] = "appReviews";
$sections["changelog"] = "appChangelog";
$sections["details"] = "appDetails";
foreach ($sections as $section => $moduleView)
{
	// Set navigation item action
	$navItem = HTML::select(".applicationInfo .pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Load application sections
	$container = HTML::select(".sectionbody")->item(0);
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

// Add action to show back button in navigation bar
$page->addReportAction("appcenter.navigation.showhide_back", 1);

// Add app info href
$page->addReportAction("appcenter.navigation.appinfo_href", $applicationInfoUrl);

// Return output
return $page->getReport();
//#section_end#
?>