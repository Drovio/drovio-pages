<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \DEV\Websites\wsServer;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get website id
$websiteID = $_GET['id'];

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
$actionFactory->setModuleAction($item, $moduleID, "genericSettings", "#settingsEditorHolder", $attr);

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
foreach ($servers as $id => $srv)
{
	$item = DOM::create('div', '', '', 'menuItem');
		DOM::attr($item, 'data-serverid', $id);
		$text = DOM::create('span', $srv['name'], '', 'serverName');
		DOM::append($item, $text);
	DOM::append($serverPool, $item);
	NavigatorProtocol::staticNav($item, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
		
	$actionFactory->setModuleAction($item, $moduleID, "serverConfig", "#settingsEditorHolder", array('id' => $websiteID, 'sid' => $id));
}

//
$holder = HTML::select('.websiteSettingsPage .settingsEditorHolder .inner')->item(0);
$module = module::loadView($moduleID, 'commonSettings');
DOM::append($holder, $module);


// Return output
return $pageContent->getReport();
//#section_end#
?>