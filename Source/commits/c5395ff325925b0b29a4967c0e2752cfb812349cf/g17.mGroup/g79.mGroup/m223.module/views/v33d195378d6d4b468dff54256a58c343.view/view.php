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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("ESS", "Environment");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \DEV\Websites\wsServer;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get website id
$websiteID = engine::getVar('id');

// Build module page
$pageContent->build("", "websiteSettingsPage", TRUE);


// Static Navigation Attributes
$nav_ref = "settingsEditorHolder";
$nav_targetcontainer = "privilegesInfo";
$nav_targetgroup = "privilegesInfo";
$nav_navgroup = "privilegesInfo";

// Generic Settings
$item = HTML::select(".websiteSettingsPage .navSidebar .projectSettings .menuItem.generic")->item(0);
NavigatorProtocol::staticNav($item, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
HTML::addClass($item, "selected");
$attr = array();
$attr['id'] = $websiteID;
$actionFactory->setModuleAction($item, $moduleID, "projectInfo", "#settingsEditorHolder", $attr);

// Meta Information
$item = HTML::select(".websiteSettingsPage .navSidebar .projectSettings .menuItem.meta")->item(0);
NavigatorProtocol::staticNav($item, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
$attr = array();
$attr['id'] = $websiteID;
$actionFactory->setModuleAction($item, $moduleID, "metaInformation", "#settingsEditorHolder", $attr);

// Advanced Settings
$item = HTML::select(".websiteSettingsPage .navSidebar .advancedSettings .menuItem.advanced a")->item(0);
$url = url::resolve("developer", "/projects/project.php");
$params = array();
$params['id'] = $websiteID;
$params['tab'] = "settings";
$url = url::get($url, $params);
DOM::attr($item, "href", $url);


// Add New server control
$item = HTML::select(".websiteSettingsPage .navSidebar .serversList .menuItem.newServer")->item(0);
NavigatorProtocol::staticNav($item, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
$attr = array();
$attr['id'] = $websiteID;
$actionFactory->setModuleAction($item, $moduleID, "serverConfig", "#settingsEditorHolder", $attr);


//
$wsServer = new wsServer($websiteID);
$servers = $wsServer->getServerList();
$holder = HTML::select('.websiteSettingsPage .navSidebar .navSection.serversList .sectionContent')->item(0);
$serverPool = DOM::create('div', '', '', 'serverPool');
DOM::append($holder, $serverPool);
foreach ($servers as $serverID => $serverInfo)
{
	$text = DOM::create('span', $serverInfo['name'], '', 'serverName');
	$item = DOM::create('div', $text, '', 'menuItem');
	DOM::data($item, 'serverid', $serverID);
	DOM::append($serverPool, $item);
	NavigatorProtocol::staticNav($item, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
	
	$attr = array();
	$attr['id'] = $websiteID;
	$attr['sid'] = $serverID;
	$actionFactory->setModuleAction($item, $moduleID, "serverConfig", "#settingsEditorHolder", $attr);
}

//
$holder = HTML::select('.websiteSettingsPage .settingsEditorHolder')->item(0);
$module = module::loadView($moduleID, 'projectInfo');
DOM::append($holder, $module);


// Return output
return $pageContent->getReport();
//#section_end#
?>