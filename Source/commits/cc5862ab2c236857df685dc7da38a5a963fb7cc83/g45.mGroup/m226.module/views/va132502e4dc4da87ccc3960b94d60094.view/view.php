<?php
//#section#[header]
// Module Declaration
$moduleID = 226;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 154;
$innerModules['appPlayer'] = 272;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\apps\application;
use \API\Profile\team;
use \UI\Modules\MPage;
use \BSS\Dashboard\appGrid;
use \BSS\Market\appMarket;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get team id
$teamID = team::getTeamID();
$teamInfo = team::info($teamID);

$page->build($teamInfo['name'], "teamDashboardPage", TRUE, TRUE);

/*
// Dashboard link
$title = moduleLiteral::get($moduleID, "lbl_dashboard");
$page->addToolbarNavItem("bsDashboard", $title, "dashboard", NULL);

// Active applications
$activeAppsTitle = moduleLiteral::get($moduleID, "lbl_activeApps", array(), FALSE);
$collection = $page->getRCollection("activeAppsContainer", $activeAppsTitle);
$title = moduleLiteral::get($moduleID, "lbl_apps");
$page->addToolbarNavItem("bsApps", $title, "activeApps", $collection, $ribbonType = "inline", $type = "obedient");
*/

// Check predefined applications
$predef = array();
$predef[] = "64";
foreach ($predef as $applicationID)
{
	$version = appMarket::getTeamAppVersion($applicationID, $live = FALSE);
	if (empty($version))
	{
		$version = appMarket::getLastApplicationVersion($applicationID);
		appMarket::buyApplication($applicationID, $version);
	}
}


// Get all team applications and add to dashboard
$teamApplications = appMarket::getTeamApplications();
$appGrid = HTML::select(".apps_grid")->item(0);
foreach ($teamApplications as $appInfo)
{
	// Get app info
	$applicationID = $appInfo['application_id'];
	$applicationVersion = $appInfo['version'];
	
	// Create application grid box
	$appBox = DOM::create("div", "", "", "appBox");
	DOM::append($appGrid, $appBox);
	
	// Application Icon
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($appBox, $ico);
	
	// Set ico image
	$appTileIcon = application::getApplicationIconUrl($applicationID, $applicationVersion);
	if (!empty($appTileIcon))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $appTileIcon);
		DOM::append($ico, $img);
	}
	
	// Application title
	$t = DOM::create("span", $appInfo['title'], "", "title");
	DOM::append($appBox, $t);
	
	// Add application data
	$applicationData = array();
	$applicationData['id'] = $applicationID;
	HTML::data($appBox, "app", $applicationData);
}

// Return output
return $page->getReport();
//#section_end#
?>