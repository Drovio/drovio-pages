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
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
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

$navItems = array();
$navItems["overview"] = array("mid" => $moduleID, "mvn" => "locOverview");
$navItems["editor"] = array("mid" => $innerModules['literalEditor'], "mvn" => "");
$navItems["translations"] = array("mid" => $innerModules['translations'], "mvn" => "");
$navItems["review"] = array("mid" => $moduleID, "mvn" => "locReview");
foreach ($navItems as $item => $itemData)
{
	// Get item action
	$refModuleID = $itemData['mid'];
	$refViewName = $itemData['mvn'];
	
	// Create reference id
	$ref = "lc_".$item;
	$targetgroup = "lc_target_group";
	
	// Get navitem
	$navItem = HTML::select(".projectLocalization .navBar .navTitle.".$item)->item(0);
	
	// Check if it is for preload
	$preload = HTML::hasClass($navItem, "selected");
	
	// Static navigation
	NavigatorProtocol::staticNav($navItem, $ref, "lcContainer", $targetgroup, "lcNav", $display = "none");
	
	// Set attributes
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['id'] = $projectID;
	$attr['editor_type'] = "embedded";
	
	// Add Module Container
	$lcContainer = HTML::select("#lcContainer")->item(0);
	$mContainer = $page->getModuleContainer($refModuleID, $refViewName, $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload);
	DOM::append($lcContainer, $mContainer);
	
	// Set group selector
	NavigatorProtocol::selector($mContainer, $targetgroup);
}

// Return output
return $page->getReport();
//#section_end#
?>