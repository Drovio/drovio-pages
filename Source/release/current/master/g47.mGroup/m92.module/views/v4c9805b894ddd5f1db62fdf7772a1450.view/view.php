<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPopup'] = 319;

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
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\account;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "appCenterSidebarContainer", TRUE);

$menuItems = array();
$menuItems["featured"] = "/";
foreach ($menuItems as $item => $url)
{
	// Get item
	$mitem = HTML::select(".appCenterSidebar .smenu .mitem.".$item)->item(0);
	
	// Set item url
	$itemUrl = url::resolve("apps", $url);
	DOM::attr($mitem, "href", $itemUrl);
	
	// Set static navigation
	$pageContent->setStaticNav($mitem, "", "", "side_navGroup", "side_navItemsGroup", $display = "none");
}

// Set all apps action
$mitem = HTML::select(".appCenterSidebar .smenu .mitem.featured")->item(0);
$actionFactory->setModuleAction($mitem, $moduleID, "featuredApps", ".appCenterContentHolder");


// Set login action
if (account::validate())
{
	$loginOuterContainer = HTML::select(".appCenterSidebar .loginOuterContainer")->item(0);
	HTML::replace($loginOuterContainer, NULL);
}
else
{
	$loginItem = HTML::select(".appCenterSidebar .loginContainer .login")->item(0);
	$actionFactory->setModuleAction($loginItem, $innerModules['loginPopup'], "", "", $attr = array(), $loading = TRUE);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>