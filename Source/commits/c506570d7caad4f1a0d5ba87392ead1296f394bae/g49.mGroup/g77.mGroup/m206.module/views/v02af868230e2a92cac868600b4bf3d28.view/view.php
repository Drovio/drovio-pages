<?php
//#section#[header]
// Module Declaration
$moduleID = 206;

// Inner Module Codes
$innerModules = array();
$innerModules['modulesAnalysis'] = 179;

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
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \DEV\Projects\project;

// Create Module Page
$page = new HTMLModulePage();
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
	$page->build("Project Not Found", "projectAnalysisPage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$ovTitle = moduleLiteral::get($moduleID, "lbl_analysisTitle", array(), FALSE);
$page->build($projectTitle." | ".$ovTitle, "projectAnalysisPage", TRUE);


// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $page->getReport();
}


// Get project type and load security manager
$projectType = $projectInfo['projectType'];
switch ($projectType)
{
	case 1:
		// Redback Core Analysis
		$analysisModuleID = $innerModules['coreAnalysis'];
		break;
	case 2:
		// Redback Modules Analysis
		$analysisModuleID = $innerModules['modulesAnalysis'];
		break;
	case 3:
		// Redback Web Engine Core SDK Analysis
		$analysisModuleID = $innerModules['webEngineAnalysis'];
		break;
	case 4:
		// Application Analysis Page
		$analysisModuleID = $innerModules['appAnalysis'];
		break;
	case 5:
		// Website Analysis Page
		$analysisModuleID = $innerModules['websiteAnalysis'];
		break;
	case 6:
		// Redback Website Template Analysis
		$analysisModuleID = $innerModules['webTemplateAnalysis'];
		break;
	case 7:
		// Redback Website Extension Analysis
		$analysisModuleID = $innerModules['webExtensionAnalysis'];
		break;
}

if (!empty($analysisModuleID))
{
	// Remove uc container
	$ucDiv = HTML::select("div.uc")->item(0);
	HTML::replace($ucDiv, NULL);
	
	// Create module container
	$attr = array();
	$attr['projectID'] = $projectID;
	$projectAnalysis = $page->getModuleContainer($analysisModuleID, $action = "", $attr, $startup = TRUE, $containerID = "projectAnalysis");
	
	// Replace main content
	$uiMainContent = HTML::select(".uiMainContent")->item(0);
	HTML::replace($uiMainContent, $projectAnalysis);
}
else
{
	$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
	$header = HTML::select("h1.title")->item(0);
	DOM::append($header, $title);
	
	$title = moduleLiteral::get($moduleID, "lbl_pageDescription");
	$header = HTML::select("h3.description")->item(0);
	DOM::append($header, $title);
	
	$title = moduleLiteral::get($moduleID, "lbl_pageUc");
	$header = HTML::select("h3.uc")->item(0);
	DOM::append($header, $title);
}

// Return output
return $page->getReport();
//#section_end#
?>