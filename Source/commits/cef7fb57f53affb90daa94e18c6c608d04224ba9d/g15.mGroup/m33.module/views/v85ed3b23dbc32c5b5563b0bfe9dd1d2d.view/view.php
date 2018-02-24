<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['geo'] = 144;
$innerModules['pages'] = 276;
$innerModules['security'] = 50;
$innerModules['publisher'] = on;
$innerModules['schemas'] = 119;
$innerModules['market'] = 275;
$innerModules['stats'] = 277;

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
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "adminHomePage", TRUE);



$actions = array();
$actions[] = "overview";
$actions[] = "pages";
$actions[] = "geo";
$actions[] = "schemas";
$actions[] = "security";
$actions[] = "stats";
$actions[] = "market";

// Set sidebar actions
foreach ($actions as $actionID)
	setSectionAction($moduleID, $actionFactory, $actionID, $innerModules[$actionID]);
	
	
// Set selected tab
$selectedTab = empty($_GET['tab']) ? "overview" : $_GET['tab'];
$boxNav = HTML::select(".adminHomePage .sideMenu .".$selectedTab)->item(0);
HTML::addClass($boxNav, "selected");

// Load content
if (isset($innerModules[$selectedTab]))
{
	$content = module::loadView($innerModules[$selectedTab]);
	$adminContent = HTML::select(".adminContent")->item(0);
	DOM::append($adminContent, $content);
}

// Return output
return $page->getReport();



function setSectionAction($moduleID, $actionFactory, $tab, $actionID)
{
	// Set url
	$url = url::resolve("admin", "/index.php");
	$params = array();
	$params['tab'] = $tab;
	$url = url::get($url, $params);
	$box = HTML::select(".adminHomePage .sideMenu .".$tab." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set static navigation
	$boxNav = HTML::select(".adminHomePage .sideMenu .".$tab)->item(0);
	NavigatorProtocol::staticNav($boxNav, "", "", "", "adminSideNav", $display = "none");
	
	// If empty module id, return
	if (!isset($actionID))
		return;
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($box, $actionID, "", ".adminContent", $attr);
	
	return $box;
}
//#section_end#
?>