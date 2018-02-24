<?php
//#section#[header]
// Module Declaration
$moduleID = 356;

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
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "tutorialsHomeContainer", TRUE);

// Load function directory
if (!function_exists("getAllTutorials"))
	$pageContent->loadView($moduleID, "tutorialsDirectory");

// Get all tutorials
$tutorials = getAllTutorials();
foreach ($tutorials as $docName => $docTitle)
{
	// Create tile
	$attr = array();
	$attr['doc'] = $docName;
	$href = url::resolve("developers", "/tutorials/".$docName);
	$ttile = $pageContent->getWeblink($href, $content = "", $target = "_self", $moduleID, $viewName = "tutorialViewer", $attr, $class = "ttile");
	$pageContent->append($ttile);
	
	// icon
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($ttile, $ico);
	
	$title = DOM::create("div", $docTitle, "", "title");
	DOM::append($ttile, $title);
}


// Return output
return $pageContent->getReport(".docTutorials .tutorialContainer");
//#section_end#
?>