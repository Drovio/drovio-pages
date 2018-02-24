<?php
//#section#[header]
// Module Declaration
$moduleID = 138;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Developer\appcenter\application;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");

// Get Application name
$appName = $_GET['name'];

if (!isset($appName))
{
	$page->build("Application Editor", "applicationEditor");
	return $page->getReport();
}

// Build the module
$page->build($appName."'s Settings", "applicationEditor");

// _____ Toolbar Navigation
$navCollection = $page->getRibbonCollection("appNav");
$subItem = $page->addToolbarNavItem("appNavSub", $appName, $class = "add_new", $navCollection, $ribbonType = "inline", $type = "obedient toggle", $pinnable = FALSE, $index = 0);


$subItem = $page->addToolbarNavItem("resourcesSub", "Resources", $class = "pages", null);
NavigatorProtocol::web($subItem, "resources.php?name=".$appName, "_blank");

$subItem = $page->addToolbarNavItem("settingsSub", "Settings", $class = "settings selected", null);

// Initialize application
$devApp = new application($appName);
$appResourcesPath = $devApp->getResourcesPath();

// Return output
return $page->getReport();
//#section_end#
?>