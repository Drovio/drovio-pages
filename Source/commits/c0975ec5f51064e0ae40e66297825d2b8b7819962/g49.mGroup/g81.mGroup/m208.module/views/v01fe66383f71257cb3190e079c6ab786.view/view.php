<?php
//#section#[header]
// Module Declaration
$moduleID = 208;

// Inner Module Codes
$innerModules = array();
$innerModules['coreConfigurator'] = 283;
$innerModules['modulesConfigurator'] = 282;
$innerModules['feedback'] = 214;
$innerModules['appsConfigurator'] = 133;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("ESS", "Environment");
importer::import("AEL", "Resources");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Testing Controller Container
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build($id = "", $class = "devControlPanel", TRUE)->get();

// Set feedback action
$header = HTML::select(".devPanel .feedback .title")->item(0);
$actionFactory->setModuleAction($header, $innerModules['feedback']);

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
		
	// Set project reference
	$referenceID = "devProjectConfigurator_prj".$project['id'];
	
	// Create projectRow
	$title = DOM::create("div", "", "", "pTitle");
	$projectRow = DOM::create("div", $title, "", "projectRow");
	DOM::append($projectContainer, $projectRow);
	DOM::attr($title, "data-ref", $referenceID);
	
	// Set static navigation attributes
	$targetContainer = "configurators";
	$targetGroup = "devControlPanel";
	$navGroup = "devControlPanelNav";
	$navDisplay = "none";
	NavigatorProtocol::staticNav($projectRow, $referenceID, $targetContainer, $targetGroup, $navGroup, $navDisplay);
	
	// Project icon
	$icon = DOM::create("span", "", "", "icon");
	DOM::append($title, $icon);
	// Add icon (if any)
	$prj = new project($project['id']);
	$projectIcon = $prj->getResourcesFolder()."/.assets/icon.png";
	if (file_exists(systemRoot.$projectIcon))
	{
		// Resolve path
		$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
		$projectIcon = url::resolve("repo", $projectIcon);
		
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIcon);
		DOM::append($icon, $img);
	}
	else
		HTML::addClass($icon, "noIcon");
	
	$projectTitle = DOM::create("h4", $project['title'], "", "title");
	DOM::append($title, $projectTitle);
	
	// Project Icon and Title
	$pointer = DOM::create("span", "", "", "pointer");
	DOM::append($title, $pointer);
	
	
	// Project configurator
	$projectConfig = HTML::select(".configurators.hpanel")->item(0);
	
	$attr = array();
	$attr['projectID'] = $project['id'];
	$attr['id'] = $project['id'];
	$devProjectConfigurator = $pageContent->getModuleContainer($configModuleID, $action = "", $attr, $startup = FALSE, $referenceID);
	HTML::addClass($devProjectConfigurator, "configLoader");
	DOM::append($projectConfig, $devProjectConfigurator);
	NavigatorProtocol::selector($devProjectConfigurator, $targetGroup);
}

return $pageContent->getReport();
//#section_end#
?>