<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPopup'] = 319;
$innerModules['dashboard'] = 185;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "navbarContainer", TRUE);

if (!account::validate())
{
	// Remove dashboard item
	$myDashboard = HTML::select("li.dashboard")->item(0);
	HTML::replace($myDashboard, NULL);
	
	// Set login popup action
	$loginItem = HTML::select("li.login")->item(0);
	$actionFactory->setModuleAction($loginItem, $innerModules['loginPopup'], "", "", $attr = array(), $loading = TRUE);
}
else
{
	$loginLI = HTML::select("li.login")->item(0);
	HTML::replace($loginLI, NULL);
	
	// Set dashboard action
	$dashboardItem = HTML::select("li.dashboard a")->item(0);
	$actionFactory->setModuleAction($dashboardItem, $innerModules['dashboard'], "", "", $attr = array(), $loading = TRUE);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>