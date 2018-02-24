<?php
//#section#[header]
// Module Declaration
$moduleID = 245;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;


// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "coreTesterPage", TRUE);


// Toolbar navigation
$title = moduleLiteral::get($moduleID, "lbl_coreConsole");
$subItem = $page->addToolbarNavItem("loaderNavSub", $title, $class = "selected", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "", ".prjContent");
NavigatorProtocol::staticNav($subItem, "", "", "", "topNav", $display = "none");


$title = moduleLiteral::get($moduleID, "lbl_testingTrunk");
$subItem = $page->addToolbarNavItem("trunkNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "trunkPage", ".prjContent");
NavigatorProtocol::staticNav($subItem, "", "", "", "topNav", $display = "none");


// Navigation attributes
$targetContainer = "ctpool";
$targetGroup = "consoleGroup";
$navGroup = "consoleNavGroup";

// Console container
$consoleContainer = HTML::select(".c_console")->item(0);
$consoleContent = module::loadView($moduleID, "console");
DOM::append($consoleContainer, $consoleContent);
NavigatorProtocol::selector($consoleContainer, $targetGroup);

// Load history logs
$historyContainer = HTML::select(".c_history")->item(0);
$historyContent = module::loadView($moduleID, "historyLog");
DOM::append($historyContainer, $historyContent);
NavigatorProtocol::selector($historyContainer, $targetGroup);


// Set navigation
$mi_console = HTML::select(".mi_console")->item(0);
NavigatorProtocol::staticNav($mi_console, "c_console", $targetContainer, $targetGroup, $navGroup, $display = "none");

$mi_history = HTML::select(".mi_history")->item(0);
NavigatorProtocol::staticNav($mi_history, "c_history", $targetContainer, $targetGroup, $navGroup, $display = "none");

// Return output
return $page->getReport();
//#section_end#
?>