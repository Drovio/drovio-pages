<?php
//#section#[header]
// Module Declaration
$moduleID = 226;

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
importer::import("BSS", "Market");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \BSS\Market\appMarket;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "enpApplicationUpdater");

// Get all team applications
$teamApplications = appMarket::getTeamApplications();
$applicationsUpdated = FALSE;
foreach ($teamApplications as $appInfo)
{
	// Get app info
	$applicationID = $appInfo['application_id'];
	$applicationVersion = $appInfo['version'];
	
	// Check if there is an update and show notifications
	$lastAppVersion = appMarket::getLastApplicationVersion($applicationID);
	if (version_compare($applicationVersion, $lastAppVersion, "<"))
	{
		// Update application
		appMarket::setTeamAppVersion($applicationID, $lastAppVersion);
		$applicationVersion = $lastAppVersion;
		
		// Set notification
		$applicationsUpdated = TRUE;
	}
}

if ($applicationsUpdated)
{
	// Add info for updates
	$updateInfo = array();
	$updateInfo['title'] = $pageContent->getLiteral("ntf_applications_updated", array(), FALSE);
	$updateInfo['action_title'] = $pageContent->getLiteral("ntf_applications_updated.more_info", array(), FALSE);
	
	// Add action to notify that some applications were updated
	$pageContent->addReportAction($name = "dashboard.applications.updated", $updateInfo);
}

return $pageContent->getReport();
//#section_end#
?>