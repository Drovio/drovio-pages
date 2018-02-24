<?php
//#section#[header]
// Module Declaration
$moduleID = 246;

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
importer::import("UI", "Modules");
importer::import("INU", "Views");
importer::import("DEV", "Profile");
//#section_end#
//#section#[code]
use \UI\Modules\MPage;
use \INU\Views\fileExplorer;
use \DEV\Profile\tester;

// Media Code
$page = new MPage($moduleID);
$page->build("", "testerTrunkPage", TRUE);

$explorer = new fileExplorer(tester::getTrunk(), "testerTrunk", "My Trunk", FALSE, FALSE);
$trunkExplorer = $explorer->build()->get();
$page->append($trunkExplorer);


return $page->getReport("", FALSE, "replace");
//#section_end#
?>