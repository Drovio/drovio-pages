<?php
//#section#[header]
// Module Declaration
$moduleID = 253;

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
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Projects");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \DEV\Literals\literal;
use \DEV\Projects\project;


// Build MContent
$pageContent = new MContent($moduleID);
$pageContainer = $pageContent->build("", "scopeExplorerContainer")->get();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	//$page->build("Project Not Found", "projectDesignerPage");
	
	// Add notification
	
	// Return report
	return $pageContainer->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $pageContainer->getReport();
}


// Add navigation bar to scope explorer
$toolbar = new navigationBar();
$navBar = $toolbar->build(navigationBar::TOP, $pageContainer)->get();
$pageContent->append($navBar);

// Refresh scopes
$refreshTool = DOM::create("span", "", "", "scTool refresh");
$tool = $toolbar->insertToolbarItem($refreshTool);

// Insert create new scope tool
$createTool = DOM::create("span", "", "", "scTool create_new");
$tool = $toolbar->insertToolbarItem($createTool);
$attr = array();
$attr['pid'] = $projectID;
//$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteObject", "", $attr);


$scopeMenu = DOM::create("ul", "", "", "scopes");
$pageContent->append($scopeMenu);
$pScopes = literal::getScopes($projectID);
foreach ($pScopes as $scope)
	$scopes[] = $scope['scope'];
	
asort($scopes);
foreach ($scopes as $scope)
{
	// Create scope item
	$li = DOM::create("li", $scope, "", "sc");
	NavigatorProtocol::staticNav($li, "", "", "", "scGroup", $display = "none");
	DOM::append($scopeMenu, $li);
	
	// Set action
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['scope'] = $scope;
	$actionFactory->setModuleAction($li, $moduleID, "scopeLiterals", ".literalsContent", $attr);
}

return $pageContent->getReport();
//#section_end#
?>