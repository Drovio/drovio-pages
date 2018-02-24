<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['pages'] = 338;
$innerModules['security'] = 50;
$innerModules['market'] = 275;
$innerModules['overview'] = 277;
$innerModules['config'] = on;
$innerModules['settings'] = 314;
$innerModules['cdn'] = 346;

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
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "adminHomePage", TRUE);
$adminContainer = HTML::select(".adminHomePage .adminHome")->item(0);


// Get selected tab
$selectedTab = empty($_GET['tab']) ? "overview" : $_GET['tab'];

// All actions
$actions = array();
$actions[] = "overview";
$actions[] = "settings";
$actions[] = "pages";
$actions[] = "security";
$actions[] = "cdn";
$actions[] = "market";

// Set sidebar actions
foreach ($actions as $action)
{
	// Add toolbar item
	$title = moduleLiteral::get($moduleID, "lbl_menuItem_".$action);
	$subItem = $page->addToolbarNavItem($action, $title, $class = $action." adminNav", NULL, $ribbonType = "float", $type = "obedient", $ico = TRUE);
	
	
	// Set url
	$url = url::resolve("admin", "/index.php");
	$params = array();
	$params['tab'] = $action;
	$url = url::get($url, $params);
	DOM::attr($subItem, "href", $url);
	
	// Set static navigation
	$ref = "adm_".$action;
	$targetgroup = "admnavgroup";
	NavigatorProtocol::staticNav($subItem, $ref, "adminHomeContainer", $targetgroup, "admNavItems", $display = "none");
	
	// Set selected
	if ($action == $selectedTab)
		HTML::addClass($subItem, "selected");
	
	// Set action
	$attr = array();
	$attr['holder'] = "#".$ref;
	$mContainer = $page->getModuleContainer($innerModules[$action], "", $attr, $startup = ($action == $selectedTab), $ref, $loading = TRUE, $preload = ($action == $selectedTab));
	HTML::addClass($mContainer, "admContainer");
	DOM::append($adminContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}



// Return output
return $page->getReport();
//#section_end#
?>