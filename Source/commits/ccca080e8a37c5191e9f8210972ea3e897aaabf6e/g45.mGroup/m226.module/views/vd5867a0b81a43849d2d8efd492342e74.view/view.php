<?php
//#section#[header]
// Module Declaration
$moduleID = 226;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("BSS", "Market");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Model\apps\application;
use \API\Profile\team;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \BSS\Market\appMarket;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "appsGrid");

// Get application name to init (if any)
$appInitName = engine::getVar("app_name");

// Get all team applications and add to dashboard
$teamApplications = appMarket::getTeamApplications();
usort($teamApplications, "sortApps");
$updates = array();
foreach ($teamApplications as $appInfo)
{
	// Get app info
	$applicationID = $appInfo['application_id'];
	$applicationVersion = $appInfo['version'];
	
	// Create application grid box
	$href = url::resolve(team::getTeamUName(), "/apps/".$appInfo['name']);
	$appBox = $pageContent->getWeblink($href, $content = "", $target = "_self", $moduleID = NULL, $viewName = "", $attr = array(), $class = "appBox");
	$pageContent->append($appBox);
	
	// Set application to init
	if (!empty($appInitName) && $appInitName == $appInfo['name'])
		HTML::addClass($appBox, "init");
	
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
	
	// Check if there is an update and show notifications
	$lastAppVersion = appMarket::getLastApplicationVersion($applicationID);
	if (version_compare($applicationVersion, $lastAppVersion, "<"))
		$updates[] = $applicationID;
}

if (count($updates) > 0)
{
	// Show notification for updates
	$ntf = new notification();
	$notification = $ntf->build($type = notification::INFO, $header = FALSE, $timeout = FALSE, $disposable = TRUE)->get();
	HTML::addClass($notification, "ntf_updates");
	
	// Set notification body
	$title = $pageContent->getLiteral("ntf_updates_available");
	$p = DOM::create("h2", $title);
	$ntf->append($p);
	
	// Append notification to grid
	$appsGrid = $pageContent->get();
	HTML::addClass($appsGrid, "updates");
	HTML::prepend($appsGrid, $notification);
}

// Return output
return $pageContent->getReport();

// Sort apps by title ascending
function sortApps($appA, $appB)
{
	return ($appA['title'] < $appB['title'] ? -1 : 1);
}
//#section_end#
?>