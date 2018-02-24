<?php
//#section#[header]
// Module Declaration
$moduleID = 304;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Model");
importer::import("DEV", "Projects");
importer::import("ESS", "Protocol");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);

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
$projectType = $projectInfo['type'];

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "applicationMarketSettings", TRUE);


// Navigation attributes
$targetcontainer = "setSections";
$targetgroup = "setNavGroup";
$navgroup = "setNav";

// Set navigation
$nav = array();
$nav["apc"] = "apcSettings";
$nav["boss"] = "bossSettings";
$nav["beta"] = "betaSettings";
$whiteBox = HTML::select(".marketSettings .whiteBox")->item(0);
foreach ($nav as $class => $viewName)
{
	$ref = $class."_ref";
	$navItem = HTML::select(".marketSettings .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer, $targetgroup, $navgroup, $display = "none");
	
	$attr = array();
	$attr['id'] = $projectID;
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

return $page->getReport();
//#section_end#
?>