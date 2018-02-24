<?php
//#section#[header]
// Module Declaration
$moduleID = 245;

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
importer::import("DEV", "Profile");
importer::import("INU", "Views");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \INU\Views\fileExplorer;
use \DEV\Profile\tester;

// Media Code
$pageContent = new MContent($moduleID);
$pageContent->build("", "testerTrunkPage");

$explorer = new fileExplorer(tester::getTrunk(), "testerTrunk", "My Trunk", FALSE, FALSE);
$trunkExplorer = $explorer->build()->get();
$pageContent->append($trunkExplorer);


return $pageContent->getReport();
//#section_end#
?>