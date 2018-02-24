<?php
//#section#[header]
// Module Declaration
$moduleID = 356;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;

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
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "docTutorialsPage", TRUE);

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = $page->loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);


// Get tutorial doc name
$docName = engine::getVar("doc");
$tutorialsContainer = HTML::select(".docTutorials .tutorialContainer")->item(0);
if (empty($docName))
{
	// Load home screen
	$mContent = $page->loadView($moduleID, "tutorialsHome");
	DOM::append($tutorialsContainer, $mContent);
}
else
{
	// Load doc viewer
	$mContent = $page->loadView($moduleID, "tutorialViewer");
	DOM::append($tutorialsContainer, $mContent);
}


// Return output
return $page->getReport();
//#section_end#
?>