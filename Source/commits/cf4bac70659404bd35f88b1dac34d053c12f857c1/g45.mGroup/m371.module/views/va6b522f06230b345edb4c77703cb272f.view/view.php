<?php
//#section#[header]
// Module Declaration
$moduleID = 371;

// Inner Module Codes
$innerModules = array();
$innerModules['teamKeys'] = 400;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Model");
importer::import("BSS", "Market");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\apps\application;
use \UI\Modules\MContent;
use \BSS\Market\appMarket;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "teamApplicationKeysContainer");

// Get all team applications and check whether they have keys enabled
$teamApplications = appMarket::getTeamApplications();
usort($teamApplications, "keySortApps");
$applicationEnabled = 0;
foreach ($teamApplications as $applicationInfo)
{
	// Get app info
	$applicationID = $applicationInfo['application_id'];
	$applicationVersion = $applicationInfo['version'];
	
	// Get application settings
	$appSettings = application::getAppSettings($applicationID, $applicationVersion);
	if ($appSettings->get("ALLOW_KEYS"))
	{
		// Create view container
		$attr = array();
		$attr['app_id'] = $applicationID;
		$mContainer = $pageContent->getModuleContainer($innerModules['teamKeys'], $viewName = "", $attr, $startup = TRUE, $containerID = "team_keys_app".$applicationID, $loading = FALSE, $preload = FALSE);
		$pageContent->append($mContainer);
		
		// Add custom class
		HTML::addClass($mContainer, "teamKeyContainer");
		
		$applicationEnabled++;
	}
}

if (!$applicationEnabled)
{
	$title = $pageContent->getLiteral("hd_noApplications");
	$hd = DOM::create("h2", $title, "", "hd");
	$pageContent->append($hd);
}

// Return output
return $pageContent->getReport();

// Sort apps by title ascending
function keySortApps($appA, $appB)
{
	return ($appA['title'] < $appB['title'] ? -1 : 1);
}
//#section_end#
?>