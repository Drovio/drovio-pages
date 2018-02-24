<?php
//#section#[header]
// Module Declaration
$moduleID = 314;

// Inner Module Codes
$innerModules = array();
$innerModules['databases'] = 315;
$innerModules['accounts'] = 316;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build("", "adminConfigurationPage", TRUE);

// Initialize menu
$navItem = HTML::select(".adminConfiguration .navigation .navItem.databases")->item(0);
HTML::addClass($navItem, "selected");

// Set static navigation
$navItems = HTML::select(".adminConfiguration .navigation .navItem");
foreach ($navItems as $navItem)
	NavigatorProtocol::staticNav($navItem, "", "", "", "configNav", $display = "none");
	
// Set module actions
$navItem = HTML::select(".adminConfiguration .navigation .navItem.databases")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['databases'], "", ".adminConfiguration .configPanes");

$navItem = HTML::select(".adminConfiguration .navigation .navItem.accounts")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['accounts'], "", ".adminConfiguration .configPanes");


// Load the default Item
$configPanes = HTML::select('.adminConfiguration .configPanes')->item(0);
$moduleView = module::loadView($innerModules['databases'], '');
DOM::append($configPanes, $moduleView);


// Return output
return $page->getReport();
//#section_end#
?>