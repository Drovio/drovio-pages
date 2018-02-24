<?php
//#section#[header]
// Module Declaration
$moduleID = 226;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 154;
$innerModules['appPlayer'] = 272;
$innerModules['teamSettings'] = 371;
$innerModules['teamMembers'] = 370;

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
importer::import("API", "Profile");
importer::import("BSS", "Dashboard");
importer::import("BSS", "Market");
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\apps\application;
use \API\Profile\team;
use \UI\Modules\MPage;
use \BSS\Dashboard\appGrid;
use \BSS\Market\appMarket;
use \DEV\Projects\projectBundle;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get team id
$teamID = team::getTeamID();
$teamInfo = team::info($teamID);
if (empty($teamInfo))
{
	// There is no team with the given id
	return $actionFactory->getReportRedirect("/", "www");
}

$teamName = strtolower($teamInfo['uname']);
$page->build($teamInfo['name'], "teamDashboardPage", TRUE, TRUE);

// Set team info to page
if (!empty($teamInfo['profile_image_url']))
{
	$teamLogo = HTML::select(".ds-navbar .team_info .logo")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	DOM::append($teamLogo, $img);
}

$teamTitle = HTML::select(".ds-navbar .team_info .title")->item(0);
HTML::innerHTML($teamTitle, $teamInfo['name']);

// Get request uri
$requestURI = $_SERVER['REQUEST_URI'];
$requestSection = trim($requestURI, "/");
$requestSection = (empty($requestSection) ? "apps" : $requestSection);

// Set main navigation
$sections = array();
$sections["settings"] = "team_settings";
$sections["members"] = "team_members";
$sections["apps"] = "apps_grid";
$mViews = array();
$mViews["settings"] = "teamSettings";
$mViews["members"] = "teamMembers";
foreach ($sections as $navClass => $sectionID)
{
	$navItem = HTML::select(".teamDashboard .navitem.".$navClass)->item(0);
	$page->setStaticNav($navItem, $sectionID, "teamDashboard", "dGroup", "dGroup", $display = "none");
	
	// Set href
	$href = url::resolve($teamName, "/".$navClass);
	DOM::attr($navItem, "href", $href);
	
	if ($navClass == $requestSection)
		HTML::addClass($navItem, "selected");
	
	$sectionItem = HTML::select(".teamDashboard .dsection#".$sectionID)->item(0);
	$page->setNavigationGroup($sectionItem, "dGroup");
	
	// Load section content
	if (isset($mViews[$navClass]))
	{
		$sectionContent = $page->loadView($innerModules[$mViews[$navClass]]);
		DOM::append($sectionItem, $sectionContent);
	}
}

// Initialize startup bundle applications
$pb = new projectBundle(4);
$projects = $pb->getProjects();
foreach ($projects as $projectInfo)
{
	$applicationID = $projectInfo['id'];
	
	$version = appMarket::getTeamAppVersion($applicationID, $live = FALSE);
	if (empty($version))
	{
		$version = appMarket::getLastApplicationVersion($applicationID);
		appMarket::buyApplication($applicationID, $version);
	}
}

// Load application grid
$appGridContainer = HTML::select(".teamDashboard .dsection#apps_grid")->item(0);
$appsGrid = $page->loadView($moduleID, "appsGrid");
DOM::append($appGridContainer, $appsGrid);

// Return output
return $page->getReport();
//#section_end#
?>