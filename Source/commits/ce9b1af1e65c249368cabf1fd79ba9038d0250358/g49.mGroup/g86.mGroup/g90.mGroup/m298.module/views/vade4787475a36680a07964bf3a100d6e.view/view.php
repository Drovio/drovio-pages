<?php
//#section#[header]
// Module Declaration
$moduleID = 298;

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
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \DEV\WebEngine\distroManager;


// Build MContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "distroListContainer")->get();


// Add navigation bar to scope explorer
$toolbar = new navigationBar();
$navBar = $toolbar->build(navigationBar::TOP, $pageContainer)->get();
$pageContent->append($navBar);

// Refresh scopes
$refreshTool = DOM::create("span", "", "", "dstTool refresh");
$tool = $toolbar->insertToolbarItem($refreshTool);

// Insert create new scope tool
$createTool = DOM::create("span", "", "", "dstTool create_new");
$tool = $toolbar->insertToolbarItem($createTool);
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($createTool, $moduleID, "createNewDistro", ".distroPackages", $attr);


// Distro list
$distroMenu = DOM::create("ul", "", "", "distros");
$pageContent->append($distroMenu);

// Get distros
$distMan = new distroManager();
$distroList = $distMan->getDistros();
foreach ($distroList as $dName => $dst)
	$distros[] = $dName;
asort($distros);
foreach ($distros as $distr)
{
	// Create scope item
	$li = DOM::create("li", $distr, "", "dst");
	NavigatorProtocol::staticNav($li, "", "", "", "dstGroup", $display = "none");
	DOM::append($distroMenu, $li);
	
	// Set action
	$attr = array();
	$attr['dname'] = $distr;
	$actionFactory->setModuleAction($li, $moduleID, "distroPackages", ".distroPackages", $attr);
}

return $pageContent->getReport();
//#section_end#
?>