<?php
//#section#[header]
// Module Declaration
$moduleID = 338;

// Inner Module Codes
$innerModules = array();
$innerModules['navigation'] = 337;

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
$pageContent->build("", "pageManagerPage", TRUE);

// Set static navigation
$navItems = HTML::select(".pageManager .navBar .navTitle");
foreach ($navItems as $navItem)
	NavigatorProtocol::staticNav($navItem, "", "", "", "pageNav", $display = "none");
	
// Set module actions
$navItem = HTML::select(".pageManager .navBar .navTitle.navigation")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['navigation'], "", ".pageManager .editorPanes", array(), $loading = TRUE);

$navItem = HTML::select(".pageManager .navBar .navTitle.errorp")->item(0);
$actionFactory->setModuleAction($navItem, $innerModules['errorp'], "", ".pageManager .editorPanes", array(), $loading = TRUE);


// Load the default Item
$configPanes = HTML::select('.pageManager .editorPanes')->item(0);
$moduleView = module::loadView($innerModules['navigation']);
DOM::append($configPanes, $moduleView);


// Return output
return $pageContent->getReport("", FALSE);
//#section_end#
?>