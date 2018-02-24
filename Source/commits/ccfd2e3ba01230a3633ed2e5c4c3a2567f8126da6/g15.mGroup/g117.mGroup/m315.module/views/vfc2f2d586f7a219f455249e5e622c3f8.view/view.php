<?php
//#section#[header]
// Module Declaration
$moduleID = 315;

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
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\settings\dbSettings;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "serverList", TRUE);


// Set server list
$serverList = HTML::select(".serverList .srvList")->item(0);
$navBar = new navigationBar();
$topNav = $navBar->build(navigationBar::TOP, $serverList)->get();
HTML::append($serverList, $topNav);

// Refresh servers
$navTool = DOM::create("span", "", "dbRefresh", "dbNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Add new server
$navTool = DOM::create("span", "", "", "dbNavTool add_new");
$navBar->insertToolbarItem($navTool);
$actionFactory->setModuleAction($navTool, $moduleID, "addServer");

// List all servers
$servers = dbSettings::getServers();
foreach ($servers as $serverName)
{
	// Create item
	$li = HTML::create("li", $serverName, "", "srvitem");
	HTML::append($serverList, $li);
	
	// Set static navigation
	NavigatorProtocol::staticNav($li, "", "", "", "dbNav", $display = "none");
	
	// Set editor action
	$attr = array();
	$attr['name'] = $serverName;
	$actionFactory->setModuleAction($li, $moduleID, "editServer", "", $attr);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>