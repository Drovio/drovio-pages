<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
importer::import("DEV", "Websites");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \DEV\Websites\website;
use \DEV\Websites\wsServer;

// Get website id and name
$websiteID = engine::getVar('id');
$websiteName = engine::getVar('name');

// Get project info
$website = new website($websiteID, $websiteName);
$websiteInfo = $website->info();

// Get project data
$websiteID = $websiteInfo['id'];
$websiteName = $websiteInfo['name'];

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "serverSettingsContainer", TRUE);


// Add New server control
$item = HTML::select(".serverSettings .serversList .menuItem.newServer")->item(0);
$pageContent->setStaticNav($item, NULL, NULL, NULL, $navGroup = "ws_srv_group", 'none');
$attr = array();
$attr['id'] = $websiteID;
$actionFactory->setModuleAction($item, $moduleID, "serverEditor", ".serverSettings .serverEditorHolder", $attr);


// List all servers
$wsServer = new wsServer($websiteID);
$servers = $wsServer->getServerList();
$holder = HTML::select('.serverSettings .serversList .sectionContent')->item(0);
$serverPool = DOM::create('div', '', '', 'serverPool');
DOM::append($holder, $serverPool);
foreach ($servers as $serverID => $serverInfo)
{
	$item = DOM::create('div', $serverInfo['name'], '', 'menuItem serverItem');
	DOM::data($item, 'sid', $serverID);
	DOM::append($serverPool, $item);
	$pageContent->setStaticNav($item, NULL, NULL, NULL, $navGroup = "ws_srv_group", 'none');
	
	$attr = array();
	$attr['id'] = $websiteID;
	$attr['sid'] = $serverID;
	$actionFactory->setModuleAction($item, $moduleID, "serverEditor", ".serverSettings .serverEditorHolder", $attr);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>