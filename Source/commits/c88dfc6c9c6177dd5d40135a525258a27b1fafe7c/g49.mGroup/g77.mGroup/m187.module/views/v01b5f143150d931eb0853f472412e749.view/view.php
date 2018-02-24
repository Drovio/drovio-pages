<?php
//#section#[header]
// Module Declaration
$moduleID = 187;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesDesigner'] = 64;
$innerModules['coreDesigner'] = 190;
$innerModules['appEngineDesigner'] = 125;
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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\projects\project;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "projectDesignerPage");

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$projectInfo = project::info($projectID, $projectName);

// Get project type and load designer
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		$designerModuleID = $innerModules['coreDesigner'];
		break;
	case 2:
		$designerModuleID = $innerModules['modulesDesigner'];
		break;
	case 3:
		$designerModuleID = $innerModules['webEngineDesigner'];
		break;
	case 4:
		$designerModuleID = $innerModules['appDesigner'];
		break;
	case 5:
		$designerModuleID = $innerModules[''];
		break;
	case 6:
		$designerModuleID = $innerModules['webTemplateDesigner'];
		break;
	case 7:
		$designerModuleID = $innerModules['webExtensionDesigner'];
		break;
	case 8:
		$designerModuleID = $innerModules['appEngineDesigner'];
		break;
}

// Create module container
$designer = $page->getModuleContainer($designerModuleID, $action = "", $attr = array(), $startup = TRUE, $containerID = "projectDesigner");
$page->append($designer);

// Return output
return $page->getReport();
//#section_end#
?>