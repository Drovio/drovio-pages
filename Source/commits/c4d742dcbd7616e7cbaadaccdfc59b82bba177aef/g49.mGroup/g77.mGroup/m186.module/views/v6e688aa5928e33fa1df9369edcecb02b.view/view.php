<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

// Inner Module Codes
$innerModules = array();
$innerModules['overview'] = 225;
$innerModules['resources'] = 205;
$innerModules['repository'] = 188;
$innerModules['analysis'] = 206;
$innerModules['security'] = 207;
$innerModules['tester'] = 212;
$innerModules['issues'] = 229;
$innerModules['members'] = 211;
$innerModules['localization'] = 252;
$innerModules['settings'] = 254;
$innerModules['statistics'] = 255;
$innerModules['market'] = 256;
$innerModules['log'] = 318;
$innerModules['releases'] = 244;
$innerModules['publisher'] = 261;

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
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "projectHomePage", TRUE);
$sectionContainer = HTML::select(".projectHomePage .prjContent")->item(0);


// Add icon (if any)
$imageBox = HTML::select(".projectHome .prjHmHeader .projectImage")->item(0);
$projectIcon = $project->getResourcesFolder()."/.assets/icon.png";
if (file_exists(systemRoot.$projectIcon))
{
	// Resolve path
	$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
	$projectIcon = url::resolve("repo", $projectIcon);
	
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectIcon);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");


// Project Title
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

// Add project status
$projectStatus = HTML::select(".projectStatusContainer .projectStatus")->item(0);
$statusClass = ($projectInfo['online'] ? "online" : "offline");
HTML::addClass($projectStatus, $statusClass);

// Add status updater
if (accountKey::validateGroup("PROJECT_ADMIN", $projectID, accountKey::PROJECT_KEY_TYPE))
{
	// Load the status switcher
	$projectStatusContainer = HTML::select(".projectStatusContainer")->item(0);
	$statusSwitcher = module::loadView($innerModules['publisher'], "setProjectStatus");
	HTML::append($projectStatusContainer, $statusSwitcher);
}

// Project Designer
if (empty($projectName))
{
	$url = url::resolve("developer", "/projects/designer.php");
	$params = array();
	$params['id'] = $projectID;
	$url = url::get($url, $params);
}
else
	$url = url::resolve("developer", "/projects/".$projectName."/designer/");
$designerBox = HTML::select(".designer a")->item(0);
DOM::attr($designerBox, "href", $url);

// Get selected tab
$selectedTab = engine::getVar('tab');
$selectedTab = empty($selectedTab) ? "overview" : $selectedTab;
$boxNav = HTML::select(".projectHomePage .prjMenu .".$selectedTab)->item(0);
HTML::addClass($boxNav, "selected");

// Set Sidebar sections
$sections = array();
$sections[] = "overview";
$sections[] = "repository";
$sections[] = "resources";
$sections[] = "tester";
$sections[] = "issues";
$sections[] = "log";
$sections[] = "analysis";
$sections[] = "statistics";
$sections[] = "localization";
$sections[] = "security";
$sections[] = "releases";
$sections[] = "members";
$sections[] = "settings";
$sections[] = "market";
foreach ($sections as $section)
{
	if (!isset($innerModules[$section]))
		return;

	// Set url
	if (empty($projectName))
	{
		$url = url::resolve("developer", "/projects/project.php");
		$params = array();
		$params['id'] = $projectID;
		$params['tab'] = $section;
		$url = url::get($url, $params);
	}
	else
		$url = url::resolve("developer", "/projects/".$projectName."/".$section."/");
	$box = HTML::select(".projectHomePage .prjMenu .".$section." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set static navigation
	$ref = "prj_".$section;
	$targetgroup = "project_section_group";
	$boxNav = HTML::select(".projectHomePage .prjMenu .".$section)->item(0);
	NavigatorProtocol::staticNav($boxNav, $ref, "prjContainer", $targetgroup, "prjNavItems", $display = "none");
	
	// Set data-ref
	HTML::data($boxNav, "ref", $section);
	
	// Add module container
	$attr = array();
	$attr['id'] = $projectID;
	$attr['name'] = $projectName;
	$attr['holder'] = "#".$ref;
	$mContainer = $page->getModuleContainer($innerModules[$section], "", $attr, $startup = ($section == $selectedTab), $ref, $loading = TRUE, $preload = ($section == $selectedTab));
	HTML::addClass($mContainer, "sectionContainer");
	DOM::append($sectionContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

// Return output
return $page->getReport();
//#section_end#
?>