<?php
//#section#[header]
// Module Declaration
$moduleID = 187;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesDesigner'] = 64;
$innerModules['coreDesigner'] = 190;
$innerModules['webEngineDesigner'] = 121;
$innerModules['appDesigner'] = 135;
$innerModules['webTemplateDesigner'] = 116;
$innerModules['webExtensionDesigner'] = 143;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \DEV\Projects\project;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

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
	$page->build("Project Not Found", "projectDesignerPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle." | Designer", "projectDesignerPage");


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}

// Source Control
$navCollection = $page->getRibbonCollection("sourceControlNav");
$title = moduleLiteral::get($moduleID, "lbl_sourceControl");
$subItem = $page->addToolbarNavItem("sourceControl", $title, $class = "vcs", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

$panel = new ribbonPanel();
$rPanel = $panel->build("commitManager", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_sourceControl_commit");
$panelItem = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($panelItem, $moduleID, "commitManager", "", $attr);
DOM::append($navCollection, $rPanel);


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
		$designerModuleID = $innerModules['webEngineDesigner'];
		break;
	case 4:
		// Application Designer Page
		$designerModuleID = $innerModules['appDesigner'];
		break;
	case 5:
		// Redirect to Website Main page
		ob_clean();
		$url = url::resolve("web", "/websites/website.php");
		$params = array();
		$params['id'] = $projectID;
		$url = url::get($url, $params);
		return $actionFactory->getReportRedirect($url, "", $formSubmit = FALSE);
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
$attr['projectID'] = $projectID;
$designer = $page->getModuleContainer($designerModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectDesigner");
$page->append($designer);

// Return output
return $page->getReport();
//#section_end#
?>