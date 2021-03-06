<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Environment");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;

// Build Module Page
$page = new MPage($moduleID);
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "applicationCenterPage", TRUE);
$actionFactory = $page->getActionFactory();

// Get app container
$appContainer = HTML::select(".applicationCenter .appContainer")->item(0);

// Get boss apps
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_appcenter_apps");
$result = $dbc->execute($q);
$apps = $dbc->fetch($result, TRUE);
foreach ($apps as $app)
{
	// Create app box weblink
	$url = url::resolve("apps", "/application.php");
	$params = array();
	$params['id'] = $app['id'];
	$url = url::get($url, $params);
	$appBox = $page->getWebLink($url, "", "_self");
	HTML::addClass($appBox, "appBox");
	DOM::append($appContainer, $appBox);
	
	$appIco = DOM::create("div", "", "", "ico");
	DOM::append($appBox, $appIco);
	
	// Get application icon
	$appIcon = projectLibrary::getPublishedPath($app['id'], $app['version'])."/resources/.assets/icon.png";
	// If file not exists, try old icon
	if (!file_exists(systemRoot.$appIcon))
		$appIcon = projectLibrary::getPublishedPath($app['id'], $app['version'])."/resources/ico.png";
	if (file_exists(systemRoot.$appIcon))
	{
		$appTileIcon = str_replace(paths::getPublishedPath(), "", $appIcon);
		$appTileIcon = url::resolve("lib", $appTileIcon);
		
		// Create icon img
		$img = DOM::create("img");
		DOM::attr($img, "src", $appTileIcon);
		DOM::append($appIco, $img);
	}
	
	// Application title
	$appTitle = DOM::create("div", $app['title'], "", "abtitle");
	DOM::append($appBox, $appTitle);
}

// Add footer
$bossMarket = HTML::select(".applicationCenter")->item(0);
$marketFooter = module::loadView($moduleID, "appCenterFooter");
DOM::append($bossMarket, $marketFooter);

return $page->getReport();
//#section_end#
?>