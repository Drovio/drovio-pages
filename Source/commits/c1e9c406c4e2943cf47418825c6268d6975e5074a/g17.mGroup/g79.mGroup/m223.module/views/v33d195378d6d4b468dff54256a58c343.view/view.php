<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;


use \ESS\Protocol\client\NavigatorProtocol;

// Create Module Page
$page = new MContent($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
//$project = new project($projectID, $projectName);
//$projectInfo = $project->info();

// If project is invalid, return error page
//if (empty($projectInfo))
//{
	// Build page
//	$page->build("Project Not Found", "websiteSettingPage");
	
	// Add notification
	
	// Return report
//	return $page->getReport();
//}
	
// Get project data
//$projectID = $projectInfo['id'];
//$projectName = $projectInfo['name'];
//$projectTitle = $projectInfo['title'];

// Build module page
//$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build("", "websiteSettingPage", TRUE);


// Check if account is valid for project
//$valid = $project->validate();
//if (!$valid)
//{
	// Add notification
	
	// Return report
//	return $page->getReport();
//}


// Static Navigation Attributes
$nav_ref = "settingsEditorHolder";
$nav_targetcontainer = "privilegesInfo";
$nav_targetgroup = "privilegesInfo";
$nav_navgroup = "privilegesInfo";

//
$holder = HTML::select('.websiteSettingPage .navSidebar .navSection.projectSettings .sectionContent')->item(0);
$item = DOM::create('div');
	$text = moduleLiteral::get($moduleID, "lbl_projectInfo");
	DOM::append($item, $text);
DOM::append($holder, $item);
$attr = array();
$attr['id'] = $projectID;
$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#privilegesInfoHolder", $attr);

//
$servers = array();
$holder = HTML::select('.websiteSettingPage .navSidebar .navSection.serversList .sectionContent')->item(0);
foreach ($servers as $server)
{
	$devItem = DOM::create('div');
	NavigatorProtocol::staticNav($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, 'none');
	DOM::append($ul, $devItem);
	
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#privilegesInfoHolder", $attr);
}

//
$holder = HTML::select('.websiteSettingPage .settingsEditorHolder .inner')->item(0);
$module = module::loadView($moduleID, 'projectSettings');
DOM::append($holder, $module);


// Return output
return $page->getReport();
//#section_end#
?>