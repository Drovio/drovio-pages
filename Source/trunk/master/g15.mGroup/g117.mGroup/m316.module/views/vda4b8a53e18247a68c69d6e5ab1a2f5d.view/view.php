<?php
//#section#[header]
// Module Declaration
$moduleID = 316;

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
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\settings\accSettings;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\treeView;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContainer = $pageContent->build("", "accountList", TRUE)->get();


// Set server list
$navBar = new navigationBar();
$topNav = $navBar->build(navigationBar::TOP, $pageContainer)->get();
$pageContent->append($topNav);

// Refresh servers
$navTool = DOM::create("span", "", "accRefresh", "accNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Add new server
$navTool = DOM::create("span", "", "", "accNavTool add_new");
$navBar->insertToolbarItem($navTool);
$actionFactory->setModuleAction($navTool, $moduleID, "addAccount");

// List all servers
$accountsList = accSettings::getAccounts();
$treeView = new treeView();
$tv = $treeView->build($id = "", $class = "")->get();
$pageContent->append($tv);
foreach ($accountsList as $accountType => $accounts)
{
	// Add type
	$treeView->insertExpandableTreeItem($accountType, $accountType, $parentId = "", $open = FALSE);
	foreach ($accounts as $accountName)
	{
		// Insert account item
		$item = $treeView->insertTreeItem($accountType."_".$accountName, $accountName, $accountType);
	
		// Set static navigation
		NavigatorProtocol::staticNav($item, "", "", "", "accNav", $display = "none");
		
		// Set editor action
		$attr = array();
		$attr['type'] = $accountType;
		$attr['name'] = $accountName;
		$actionFactory->setModuleAction($item, $moduleID, "editAccount", "", $attr);
	}
}


// Return output
return $pageContent->getReport();
//#section_end#
?>