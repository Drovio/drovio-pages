<?php
//#section#[header]
// Module Declaration
$moduleID = 285;

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
importer::import("API", "Literals");
importer::import("DEV", "Websites");
importer::import("UI", "Developer");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \DEV\Websites\website;
use \DEV\Websites\pages\wsPage;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\frames\windowFrame;
use \UI\Developer\devTabber;

// Create Module Page
$mContent = new MContent();
$actionFactory = $mContent->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$mContent->build("", 'websitePageEditor', FALSE);


// Build Grid Splitter
$gridSplitter = new gridSplitter();
$gridSplitter->build(gridSplitter::ORIENT_HOZ, gridSplitter::SIDE_LEFT, $closed = FALSE, $sideTitle = "Page Explorer");
$mContent->append($gridSplitter->get());

// Grid Splitter side content
$attr = array();
$attr['id'] = $projectID;
$attr['pid'] = $projectID;
$viewerContainer = $mContent->getModuleContainer($moduleID, "pagesExplorer", $attr);
$gridSplitter->appendToSide($viewerContainer);

// WIDE
$wide = new devTabber();
$pagesWide = $wide->build($id = "pgsTabber", $withBorder = FALSE)->get();
$gridSplitter->appendToMain($pagesWide);

// Return output
return $mContent->getReport();
//#section_end#
?>