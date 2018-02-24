<?php
//#section#[header]
// Module Declaration
$moduleID = 208;

// Inner Module Codes
$innerModules = array();
$innerModules['coreConfigurator'] = 209;
$innerModules['modulesConfigurator'] = 210;
$innerModules['feedback'] = 214;
$innerModules['appsConfigurator'] = 133;

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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Testing Controller Container
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build($id = "", $class = "devControlPanel", TRUE)->get();


$title = moduleLiteral::get($moduleID, "lbl_devFeedback");
$header = HTML::select(".devPanel .feedback .title")->item(0);
DOM::append($header, $title);
$actionFactory->setModuleAction($header, $innerModules['feedback']);

$title = moduleLiteral::get($moduleID, "lbl_devProjects");
$header = HTML::select(".devPanel .projects .title")->item(0);
DOM::append($header, $title);

// Get project container
$projectContainer = HTML::select(".devPanel .projects .list")->item(0);

// Get account's projects
// List projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$myProjects = $dbc->fetch($result, TRUE);

// Foreach project, build the project developer tool navigator 
foreach ($myProjects as $project)
{
	// Add module container for each project type
	switch ($project['projectType'])
	{
		case 1:
			// Redback Core Configurator
			$configModuleID = $innerModules['coreConfigurator'];
			break;
		case 2:
			// Redback Modules Configurator
			$configModuleID = $innerModules['modulesConfigurator'];
			break;
		case 3:
			// Redback Web Engine Core SDK Configurator
			$configModuleID = NULL;//$innerModules[''];
			break;
		case 4:
			// Application Configurator
			$configModuleID = $innerModules['appsConfigurator'];
			break;
		case 5:
			// Website Configurator
			$configModuleID = NULL;//$innerModules[''];
			break;
		case 6:
			// Redback Website Template Configurator
			$configModuleID = NULL;//$innerModules[''];
			break;
		case 7:
			// Redback Website Extension Configurator
			$configModuleID = NULL;//$innerModules[''];
			break;
	}
	
	// If there is no configurator for this module, continue to next
	if (is_null($configModuleID))
		continue;
	
	// Create projectRow
	$title = DOM::create("div", "", "", "pTitle");
	$projectRow = DOM::create("div", $title, "", "projectRow");
	DOM::append($projectContainer, $projectRow);
	
	// Project Icon and Title
	$icon = DOM::create("span", "", "", "icon");
	DOM::append($title, $icon);
	
	$projectTitle = DOM::create("h4", $project['title'], "", "title");
	DOM::append($title, $projectTitle);
	
	
	// Project configurator
	$projectConfig = DOM::create("div", "", "", "configurator");
	DOM::append($projectRow, $projectConfig);
	
	$attr = array();
	$attr['projectID'] = $project['id'];
	$devProjectConfigurator = $pageContent->getModuleContainer($configModuleID, $action = "", $attr, $startup = FALSE, $containerID = "devProjectConfigurator_prj".$project['id']);
	DOM::append($projectConfig, $devProjectConfigurator);
}

return $pageContent->getReport();
//#section_end#
?>