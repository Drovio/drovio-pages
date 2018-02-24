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
importer::import("UI", "Forms");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \UI\Forms\formReport\formNotification;
use \DEV\Websites\settings\metaSettings;

// Initialize website id and website settings
$websiteID = engine::getVar("id");
$metaSettings = new metaSettings($websiteID);

if (engine::isPost())
{
	// Update open graph information
	$ogEnabled = (isset($_POST['enabled']) ? 1 : 0);
	$metaSettings->set("meta_og_enabled", $ogEnabled);
	$metaSettings->set("meta_og_sitename", $_POST['site_name']);
	$metaSettings->set("meta_og_type", $_POST['type']);
	$metaSettings->set("meta_og_image", $_POST['image']);
	
	// Build success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
//#section_end#
?>