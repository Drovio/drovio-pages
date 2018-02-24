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
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "adminConfigurationPage", TRUE);

// Set static navigation
$navItems = HTML::select(".adminConfiguration .navBar .navTitle");
foreach ($navItems as $navItem)
	NavigatorProtocol::staticNav($navItem, "", "", "", "configNav", $display = "none");
	
// Set module actions
$navItem = HTML::select(".adminConfiguration .navBar .navTitle.databases")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['databases'], "", ".adminConfiguration .configPanes", array(), $loading = TRUE);

$navItem = HTML::select(".adminConfiguration .navBar .navTitle.accounts")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['accounts'], "", ".adminConfiguration .configPanes", array(), $loading = TRUE);


// Load the default Item
$configPanes = HTML::select('.adminConfiguration .configPanes')->item(0);
$moduleView = module::loadView($innerModules['databases']);
DOM::append($configPanes, $moduleView);


// Return output
return $pageContent->getReport("", FALSE);
//#section_end#
?>