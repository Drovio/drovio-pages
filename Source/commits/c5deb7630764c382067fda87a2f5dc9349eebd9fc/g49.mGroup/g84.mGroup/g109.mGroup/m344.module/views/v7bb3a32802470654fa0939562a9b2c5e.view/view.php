<?php
//#section#[header]
// Module Declaration
$moduleID = 344;

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
importer::import("DEV", "Core");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \DEV\Core\manifests;
use \DEV\Core\coreProject;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "mfExplorerContainer", TRUE);


// Set manifest explorer toolbar
$navBar = new navigationBar();
$topNav = $navBar->build(navigationBar::TOP, $pageContent->get())->get();
$pageContent->append($topNav);

// Refresh servers
$navTool = DOM::create("span", "", "mfRefresh", "mfNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Add new server
$navTool = DOM::create("span", "", "", "mfNavTool add_new");
$navBar->insertToolbarItem($navTool);
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$actionFactory->setModuleAction($navTool, $moduleID, "createManifest", "", $attr, $loading = TRUE);

// Get all manifests
$mfManager = new manifests();
$manifestList = $mfManager->getAll();
$mfExplorer = HTML::select(".mfExplorer")->item(0);
foreach ($manifestList as $mfID => $manifest)
{
	// Create item content
	$mfItem = DOM::create("li", "", "", "mfItem");
	DOM::append($mfExplorer, $mfItem);
	
	// Check if manifest is enabled
	if (!$manifest['info']['enabled'])
		HTML::addClass($mfItem, "disabled");
		
	// Add ico
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($mfItem, $ico);
	
	// Add title
	$title = DOM::create("div", $manifest['info']['name'], "", "title");
	DOM::append($mfItem, $title);
	
	// Static navigation attributes
	$pageContent->setStaticNav($mfItem, "", "", "", "mfNav", $display = "none");
	
	// Set action
	$attr = array();
	$attr['id'] = coreProject::PROJECT_ID;
	$attr['mfid'] = $mfID;
	$actionFactory->setModuleAction($mfItem, $moduleID, "manifestEditor", ".coreManifests .mfContainer", $attr, $loading = TRUE);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>