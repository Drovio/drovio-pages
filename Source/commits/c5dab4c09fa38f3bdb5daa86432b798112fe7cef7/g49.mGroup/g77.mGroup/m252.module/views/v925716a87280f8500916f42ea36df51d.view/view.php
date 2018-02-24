<?php
//#section#[header]
// Module Declaration
$moduleID = 252;

// Inner Module Codes
$innerModules = array();

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
importer::import("ESS", "Protocol");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "projectLocalizationPage", TRUE);


// Set navigation
$targetContainer = "navContent";
$targetGroup = "locGroup";
$navGroup = "locNavGroup";

$item = HTML::select(".projectLocalizationPage li.overview")->item(0);
NavigatorProtocol::staticNav($item, "loverview", $targetContainer, $targetGroup, $navGroup, $display = "none");
$item = HTML::select(".projectLocalizationPage li.translations")->item(0);
NavigatorProtocol::staticNav($item, "ltranslations", $targetContainer, $targetGroup, $navGroup, $display = "none");
$item = HTML::select(".projectLocalizationPage li.review")->item(0);
NavigatorProtocol::staticNav($item, "lreview", $targetContainer, $targetGroup, $navGroup, $display = "none");

$attr = array();
$attr['pid'] = $projectID;

// Set group selectors
$content = HTML::select("#loverview")->item(0);
NavigatorProtocol::selector($content, $targetGroup);
$mContainer = $page->getModuleContainer($moduleID, "locOverview", $attr);
DOM::append($content, $mContainer);

// Set group selectors
$content = HTML::select("#ltranslations")->item(0);
NavigatorProtocol::selector($content, $targetGroup);
$mContainer = $page->getModuleContainer($moduleID, "locTranslations", $attr);
DOM::append($content, $mContainer);

// Set group selectors
$content = HTML::select("#lreview")->item(0);
NavigatorProtocol::selector($content, $targetGroup);

// Return output
return $page->getReport();
//#section_end#
?>