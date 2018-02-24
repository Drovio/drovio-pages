<?php
//#section#[header]
// Module Declaration
$moduleID = 199;

// Inner Module Codes
$innerModules = array();
$innerModules['websiteObject'] = 197;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("INU", "Views");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \INU\Views\fileExplorer;
use \DEV\Websites\website;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$websiteID = $_REQUEST['id'];
$websiteName = $_REQUEST['name'];

// Get project info
$website = new website($websiteID, $websiteName);
$websiteInfo = $website->info();
	
// Get project data
$websiteID = $websiteInfo['id'];
$websiteName = $websiteInfo['name'];
$websiteTitle = $websiteInfo['title'];


// Build module page
$pgTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pgTitle." | ".$websiteTitle, "websiteResourcesPage");


// Create a file explorer
$resourcesFolder = $website->getResourcesFolder();
$explorer = new fileExplorer($resourcesFolder, "wsRsrc_".$websiteID, $websiteTitle." Resources", FALSE, FALSE);
$websiteResourcesExplorer = $explorer->build()->get();
$page->append($websiteResourcesExplorer);

// Return output
return $page->getReport($_GET['holder'], FALSE);
//#section_end#
?>