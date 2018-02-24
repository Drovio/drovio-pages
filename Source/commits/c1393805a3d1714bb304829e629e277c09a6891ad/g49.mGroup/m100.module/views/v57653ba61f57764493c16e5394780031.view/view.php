<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['devDocs'] = 99;
$innerModules['devHome'] = 100;
$innerModules['devTools'] = 203;
$innerModules['devSupport'] = 193;
$innerModules['devProfile'] = 191;

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;

// Create Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module
$pageContent->build("toolbarDeveloperNav", "developerNav", TRUE);

// Set item actions
addAction("devHome", $actionFactory, $innerModules['devHome']);
addAction("devDocs", $actionFactory, $innerModules['devDocs']);
addAction("devSupport", $actionFactory, $innerModules['devSupport']);

// Return output
return $pageContent->getReport();


// Add navigation action
function addAction($itemClass, $actionFactory, $moduleID)
{
	if (!is_null($moduleID))
	{
		$item = HTML::select(".".$itemClass." a")->item(0);
		$actionFactory->setModuleAction($item, $moduleID);
	}
}
//#section_end#
?>