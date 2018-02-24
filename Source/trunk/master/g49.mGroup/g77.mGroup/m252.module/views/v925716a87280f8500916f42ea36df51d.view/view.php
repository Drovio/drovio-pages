<?php
//#section#[header]
// Module Declaration
$moduleID = 252;

// Inner Module Codes
$innerModules = array();
$innerModules['translations'] = 347;
$innerModules['literalEditor'] = 253;

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
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

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
$whiteBox = HTML::select(".projectLocalization .whiteBox")->item(0);

$navItems = array();
$navItems["overview"] = array("mid" => $moduleID, "mvn" => "locOverview");
$navItems["editor"] = array("mid" => $innerModules['literalEditor'], "mvn" => "");
$navItems["translations"] = array("mid" => $innerModules['translations'], "mvn" => "");
$navItems["preferences"] = array("mid" => $innerModules['translations'], "mvn" => "trPreferences");
foreach ($navItems as $class => $navData)
{
	// Get item action
	$refModuleID = $navData['mid'];
	$refViewName = $navData['mvn'];
	
	$ref = $class."_ref";
	$navItem = HTML::select(".projectLocalization .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "lcContainer", $targetgroup = "mGroup", $navgroup = "prj_lc_Group", $display = "none");
	
	$attr = array();
	$attr['id'] = $projectID;
	$attr['pid'] = $projectID;
	$attr['editor_type'] = "embedded";
	$preload = HTML::hasClass($navItem, "selected");
	$mContainer = $page->getModuleContainer($refModuleID, $refViewName, $attr, $startup = TRUE, $ref, $loading = FALSE, $preload);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>