<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

// Inner Module Codes
$innerModules = array();
$innerModules['appInfo'] = 358;
$innerModules['applicationPlayer'] = 169;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Model\apps\application;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Resources\paths;
use \DEV\Projects\projectLibrary;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "featuredAppsContainer", TRUE);
$appContainer = HTML::select(".featuredAppsContainer .appList")->item(0);

// Get boss apps
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_appcenter_apps");
$result = $dbc->execute($q);

$rowMaxObjects = 4;
$count = 0;
while ($app = $dbc->fetch($result))
{
	// Increase counter and choose whether to create a new row
	$count++;
	$index = $count % $rowMaxObjects;
	
	// If modulo == 1 then it's the first item for a new row, create new row
	if ($index == 1)
	{
		// Create new row
		$row = DOM::create('div', '', '', 'row');
		DOM::append($appContainer, $row);
	}
	
	// Create application tile
	$appBox = getAppBox($app, $page, $moduleID, $innerModules, $actionFactory);
	DOM::append($row, $appBox);
}

// Add action to show back button in navigation bar
$page->addReportAction("appcenter.navigation.showhide_back", 0);

// Return output
return $page->getReport();

function getAppBox($app, $page, $moduleID, $innerModules, $actionFactory)
{
	// Create application tile
	$appBox = DOM::create("div", "", "", "appTile");
	
	// Get Application Main Url
	if (!empty($app['name']))
		$url = url::resolve("apps", "/".$app['name']);
	else
	{
		$params = array(); 
		$params['id'] = $app['id'];
		$url = url::resolve("apps", "/application.php", $params);
	}
	$appBoxMain = $page->getWebLink($url, "", "_self", NULL, "", array(), "bmain");
	DOM::append($appBox, $appBoxMain);
	
	// Set module action
	$attr = array();
	$attr['id'] = $app['id'];
	$attr['name'] = $app['name'];
	$actionFactory->setModuleAction($appBoxMain, $innerModules['appInfo'], "applicationInfoViewer", ".appCenterContentHolder", $attr);
	
	// Get application icon
	$appIconUrl = application::getApplicationIconUrl($app['id'], $app['version']);
	if (!empty($appIconUrl))
	{
		// Create icon img
		$img = DOM::create("img");
		DOM::attr($img, "src", $appIconUrl);
		DOM::append($appIco, $img);
	}
	$appIco = DOM::create("div", $img, "", "appIcon");
	DOM::append($appBoxMain, $appIco);
			
	$appInfo = DOM::create("div", "", "", "appInfo");
	DOM::append($appBoxMain, $appInfo);
	
	$appTitle = DOM::create("div", $app['title'], "", "appTitle");
	DOM::append($appInfo, $appTitle);
	
	// Owner Team
	$appDevs = DOM::create('div', $app["teamName"], '', 'appOwner');
	DOM::append($appInfo, $appDevs);
	
	// Application Control
	$appControls = DOM::create('div', '', '', 'appControls');
	DOM::append($appBox, $appControls);	
	
	// Get Application Title 'Info' Link
	if (!empty($app['name']))
		$url = url::resolve("apps", "/".$app['name']."/play");
	else
	{
		$params = array(); 
		$params['id'] = $app['id'];
		$url = url::resolve("apps", "/player.php", $params);
	}
	$title = moduleLiteral::get($moduleID, "lbl_playApp");
	$playBtn = $page->getWebLink($url, $title, "_self", NULL, "", array(), "playBtn");
	DOM::append($appControls, $playBtn);
	
	// Set action to button
	$attr = array();
	$attr['id'] = $app['id'];
	$attr['name'] = $app['name'];
	$actionFactory->setModuleAction($playBtn, $innerModules['applicationPlayer'], "playApp", ".appCenterContentHolder", $attr, $loading = TRUE);
	
	return $appBox;
}
//#section_end#
?>