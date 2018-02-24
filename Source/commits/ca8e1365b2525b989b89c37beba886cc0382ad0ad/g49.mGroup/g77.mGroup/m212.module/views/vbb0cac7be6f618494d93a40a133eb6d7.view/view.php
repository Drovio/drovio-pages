<?php
//#section#[header]
// Module Declaration
$moduleID = 212;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesTesterPreview'] = 246;
$innerModules['coreTesterPreview'] = 245;
$innerModules['appTesterPreview'] = 137;

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
importer::import("UI", "Content");
importer::import("ESS", "Environment");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \ESS\Environment\session;
use \API\Literals\moduleLiteral;
use \UI\Content\HTMLFrame;
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
$ovTitle = moduleLiteral::get($moduleID, "lbl_testerPreviewTitle", array(), FALSE);
$page->build($ovTitle." | ".$projectTitle, "projectTesterPreviewPage", TRUE);

// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Tester
		$testerModuleID = $innerModules['coreTesterPreview'];
		break;
	case 2:
		// Redback Modules Tester
		$testerModuleID = $innerModules['modulesTesterPreview'];
		break;
	case 3:
		// Redback Web Engine Core SDK Tester
		$testerModuleID = $innerModules['webEngineTesterPreview'];
		break;
	case 4:
		// Application Tester Page
		//$withFrame = TRUE;
		$testerModuleID = $innerModules['appTesterPreview'];
		break;
	case 5:
		// Website Tester Page
		$withFrame = TRUE;
		$testerModuleID = $innerModules['websiteTesterPreview'];
		break;
	case 6:
		// Redback Website Template Tester
		$withFrame = TRUE;
		$testerModuleID = $innerModules['webTemplateTesterPreview'];
		break;
	case 7:
		// Redback Website Extension Tester
		$withFrame = TRUE;
		$testerModuleID = $innerModules['webExtensionTesterPreview'];
		break;
}

// Check whether the tester will load frame
if ($withFrame)
{
	// Create token and set to session
	$token = md5("developer_".$projectID."_tester_".microtime(TRUE));
	session::set("tester_project_".$projectID, $value = $token, $namespace = 'developer_tester');
	
	// Set frame source
	$frameSource = url::resolve("developer", "/projects/preview.php");
	$params = array();
	$params['id'] = $projectID;
	$params['token'] = $token;
	$frameSource = url::get($frameSource, $params);
	
	// Create a frame for the tester
	$frame = new HTMLFrame();
	$testerFrame = $frame->build($frameSource, $name = "", $id = "", $class = "", $sandbox = array())->get();
	$page->append($testerFrame);
}
else
{
	// Create module container
	$attr = array();
	$attr['projectID'] = $projectID;
	$testerPreview = $page->getModuleContainer($testerModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectTesterPreview");
	$page->append($testerPreview);
}

// Return output
return $page->getReport();
//#section_end#
?>