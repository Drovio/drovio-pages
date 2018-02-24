<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
importer::import("API", "Model");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Model\modules\module;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "repositoryMainView", TRUE);


// Set section navigation
$sections = array();
$sections[] = "overview";
$sections[] = "commits";
$sections[] = "branches";
$sections[] = "statistics";

foreach ($sections as $section)
{
	// Load view
	$mContentHolder = HTML::select("#".$section."Panel")->item(0);
	$mContent = module::loadView($moduleID, "repo_".$section."Panel");
	HTML::append($mContentHolder, $mContent);
	
	// Add static navigation selector
	NavigatorProtocol::selector($mContentHolder, "repoGroup");
	
	// Add static navigation
	$navitem = HTML::select(".navitem.".$section)->item(0);
	NavigatorProtocol::staticNav($navitem, $section."Panel", "repoContainer", "repoGroup", "repoNavGroup", $display = "none");
}

// Return output
return $pageContent->getReport();
//#section_end#
?>