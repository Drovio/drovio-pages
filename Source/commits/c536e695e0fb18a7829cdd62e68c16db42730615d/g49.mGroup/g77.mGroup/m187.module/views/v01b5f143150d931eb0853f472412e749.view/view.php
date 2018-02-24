<?php
//#section#[header]
// Module Declaration
$moduleID = 187;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesDesigner'] = 240;
$innerModules['coreDesigner'] = 233;
$innerModules['appDesigner'] = 135;
$innerModules['webTemplateDesigner'] = 116;
$innerModules['webExtensionDesigner'] = 142;
$innerModules['webCoreDesigner'] = 231;
$innerModules['literalEditor'] = 253;
$innerModules['projectHome'] = 186;
$innerModules['projectHints'] = 322;
$innerModules['webDesigner'] = 196;

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
importer::import("ESS", "Environment");
importer::import("UI", "Core");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \UI\Core\components\ribbon\rPanel;
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

// Check friendly url
if (empty($projectName) && !empty($projectInfo['name']))
{
	$projectName = $projectInfo['name'];
	$url = url::resolve("developers", "/dashboard/".$projectName."/designer/");
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build("Designer | ".$projectTitle, "projectDesignerPage", FALSE, TRUE);

// Project Home
$attr = array();
$attr['id'] = $projectID;
$attr['name'] = $projectName;
$collection = $page->getRCollection("ProjectDesignerHomeNav", $title = "", $moduleID, $viewName = "TLB_project", $startup = TRUE, $attr);
$subItem = $page->addToolbarNavItem("projectHome", $projectTitle, $class = "project", $collection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

// Source Control
$navCollection = $page->getRibbonCollection("sourceControlNav");
$title = moduleLiteral::get($moduleID, "lbl_sourceControl");
$subItem = $page->addToolbarNavItem("sourceControl", $title, $class = "vcs", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);

$panel = new rPanel();
$rPanel = $panel->build("commitManager", TRUE)->get();
// Commit Manager
$title = moduleLiteral::get($moduleID, "lbl_sourceControl_commit");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($panelItem, $moduleID, "commitManager", "", $attr, $loading = TRUE);
// History Manager
$title = moduleLiteral::get($moduleID, "lbl_sourceControl_history");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($panelItem, $moduleID, "historyManager", "", $attr, $loading = TRUE);
DOM::append($navCollection, $rPanel);

// Literal Management
$title = moduleLiteral::get($moduleID, "lbl_literals");
$subItem = $page->addToolbarNavItem("literals", $title, $class = "literals", NULL, $ribbonType = "float", $type = "obedient", $ico = TRUE);
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($subItem, $innerModules['literalEditor'], "", "", $attr, $loading = TRUE);


// Get project type and load designer
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Designer
		$designerModuleID = $innerModules['coreDesigner'];
		break;
	case 2:
		// Redback Modules Designer
		$designerModuleID = $innerModules['modulesDesigner'];
		break;
	case 3:
		// Redback Web Engine Core SDK
		$designerModuleID = $innerModules['webCoreDesigner'];
		break;
	case 4:
		// Application Designer Page
		$designerModuleID = $innerModules['appDesigner'];
		break;
	case 5:
		// Website Designer Page
		$designerModuleID = $innerModules['webDesigner'];
		break;
	case 6:
		// Redback Website Template Designer
		$designerModuleID = $innerModules['webTemplateDesigner'];
		break;
	case 7:
		// Redback Website Extension Designer
		$designerModuleID = $innerModules['webExtensionDesigner'];
		break;
}

// Create module container
$attr = array();
$attr['id'] = $projectID;
$attr['name'] = $projectName;
$attr['projectID'] = $projectID;
$designer = $page->getModuleContainer($designerModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectDesigner", $loading = TRUE, $preload = TRUE);
$page->append($designer);


// Add project hints
$attr = array();
$attr['id'] = $projectID;
$hints = $page->getModuleContainer($moduleID, $action = "hintsPopup", $attr, $startup = TRUE, $containerID = "projectHintsContainer");
$page->append($hints);

// Return output
return $page->getReport();
//#section_end#
?>