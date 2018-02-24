<?php
//#section#[header]
// Module Declaration
$moduleID = 196;

// Inner Module Codes
$innerModules = array();
$innerModules['source'] = 278;
$innerModules['templates'] = 220;
$innerModules['themes'] = 222;
$innerModules['settings'] = 223;
$innerModules['resources'] = 199;
$innerModules['overview'] = 279;
$innerModules['pages'] = 285;
$innerModules['literalEditor'] = 253;

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
importer::import("UI", "Core");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Core\components\ribbon\rPanel;
use \UI\Modules\MPage;
use \DEV\Websites\website;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Build page
	$page->build("Project Not Found", "projectHomePage");
	
	// Add notification
	
	// Return report
	return $page->getReport();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "websiteDesingerPage", TRUE);
$webContainer = HTML::select(".websiteDesingerPage .webContainer")->item(0);


// Get selected tab
$selectedTab = empty($_GET['tab']) ? "overview" : $_GET['tab'];

// Set item actions
$actions = array();
$actions[] = "overview";
$actions[] = "pages";
$actions[] = "templates";
$actions[] = "themes";
$actions[] = "source";
$actions[] = "resources";

// Set sidebar actions
foreach ($actions as $action)
{
	// Add toolbar item
	if ($action == "overview")
		$title = $projectTitle;
	else
		$title = moduleLiteral::get($moduleID, "lbl_menuItem_".$action);
	$subItem = $page->addToolbarNavItem($action, $title, $class = $action." wsNav", NULL, $ribbonType = "float", $type = "obedient", $ico = TRUE);
	
	
	// Set url
	$url = url::resolve("web", "/websites/website.php");
	$params = array();
	$params['id'] = $projectID;
	$params['tab'] = $action;
	$url = url::get($url, $params);
	DOM::attr($subItem, "href", $url);
	
	// Set static navigation
	$ref = "ws_".$action;
	$targetcontainer = "webContainer";
	$targetgroup = "webnavgroup";
	NavigatorProtocol::staticNav($subItem, $ref, $targetcontainer, $targetgroup, "webNavItems", $display = "none");
	
	// Set selected
	if ($action == $selectedTab)
		HTML::addClass($subItem, "selected");
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['holder'] = "#".$ref;
	$mContainer = $page->getModuleContainer($innerModules[$action], "", $attr, $startup = ($action == $selectedTab), $ref, $loading = TRUE);
	HTML::addClass($mContainer, "wsContainer");
	DOM::append($webContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

// Literals Menu Extra Item
$title = moduleLiteral::get($moduleID, "lbl_websiteLiteralsTitle");
$subItem = $page->addToolbarNavItem("literalControl", $title, $class = "literals", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($subItem, $innerModules['literalEditor'], "", "", $attr);

// Settings Menu Extra Item
$title = moduleLiteral::get($moduleID, "lbl_websiteSettingsTitle");
$subItem = $page->addToolbarNavItem("settingControl", $title, $class = "settings", $navCollection, $ribbonType = "float", $type = "obedient toggle", $ico = TRUE);
	

// Load Settings Content
$content = module::loadView($innerModules['settings']);
$settingsPane = HTML::select(".websiteDesingerPage .settingsPanel")->item(0);
DOM::append($settingsPane, $content);



// Return output
return $page->getReport();
//#section_end#
?>