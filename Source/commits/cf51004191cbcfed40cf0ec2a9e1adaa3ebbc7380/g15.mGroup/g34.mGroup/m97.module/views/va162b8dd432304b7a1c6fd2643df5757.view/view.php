<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;

// Build the Module Page
$pageContent = new MContent($moduleID);
$pageContent->build("", "modulePrivilegesPage", TRUE);
$actionFactory = $pageContent->getActionFactory();


$sidebar = HTML::select(".modulePrivilegesPage .sidebar")->item(0);
$mainNav = HTML::select(".modulePrivilegesPage .mainContent")->item(0);

$navTree = new treeView();
$moduleGroupTree = $navTree->build()->get();
DOM::append($sidebar, $moduleGroupTree);

// Get Module Groups
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_all_mgroups");
$result = $dbc->execute($dbq, $attr);
$moduleGroups = $dbc->fetch($result, TRUE);
foreach ($moduleGroups as $group)
{
	// Insert group item
	$groupName = DOM::create("div", $group['description']);
	$parentID = ($group['depth'] == 0 ? "" : 'mg_'.$group['parent_id']);
	$groupItem = $navTree->insertSemiExpandableTreeItem('mg_'.$group['id'], $groupName, $parentID);
	$navTree->assignSortValue($groupItem, $group['description']);
	
	// Set module action
	$attr = array();
	$attr['gid'] = $group['id'];
	$actionFactory->setModuleAction($groupName, $moduleID, "moduleViewer", "#moduleViewer", $attr, $loading = TRUE);
}


// Create module Viewer
$moduleViewer = DOM::create("div", "", "moduleViewer");
DOM::append($mainNav, $moduleViewer);


return $pageContent->getReport();
//#section_end#
?>