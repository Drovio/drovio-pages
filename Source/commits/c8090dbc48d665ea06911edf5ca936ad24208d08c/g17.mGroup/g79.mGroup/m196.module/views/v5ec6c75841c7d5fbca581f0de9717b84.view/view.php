<?php
//#section#[header]
// Module Declaration
$moduleID = 196;

// Inner Module Codes
$innerModules = array();
$innerModules['source'] = 278;
$innerModules['settings'] = 223;
$innerModules['resources'] = 199;
$innerModules['overview'] = 279;
$innerModules['pages'] = 285;
$innerModules['localization'] = 252;
$innerModules['preview'] = 361;

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
importer::import("DEV", "Websites");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Core");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
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
$page->build($projectTitle, "webDesignerPage", TRUE);
$sectionContainer = HTML::select(".webDesigner .webContent")->item(0);


// Add icon (if any)
$imageBox = HTML::select(".webDesigner .website-navbar .projectImage")->item(0);
$projectIconUrl = $project->getIconUrl();
if (!empty($projectIconUrl))
{
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectIconUrl);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");
	

// Project Title
$pTitle = HTML::select(".webDesigner .website-navbar .projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

// Get selected tab
$selectedTab = engine::getVar('tab');
$selectedTab = empty($selectedTab) ? "overview" : $selectedTab;
$boxNav = HTML::select(".webDesignerPage .webMenu .".$selectedTab)->item(0);
HTML::addClass($boxNav, "selected");

// Set Sidebar sections
$sections = array();
$sections[] = "overview";
$sections[] = "pages";
$sections[] = "templates";
$sections[] = "themes";
$sections[] = "extensions";
$sections[] = "source";
$sections[] = "preview";
$sections[] = "resources";
$sections[] = "settings";
foreach ($sections as $section)
{
	if (!isset($innerModules[$section]))
		continue;

	// Set url
	if (empty($projectName))
	{
		$url = url::resolve("developers", "/dashboard/designer.php");
		$params = array();
		$params['id'] = $projectID;
		$params['tab'] = $section;
		$url = url::get($url, $params);
	}
	else
		$url = url::resolve("developers", "/dashboard/".$projectName."/designer/".$section."/");
	$box = HTML::select(".webDesignerPage .webMenu .".$section." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set static navigation
	$ref = "web_".$section;
	$targetgroup = "website_section_group";
	$boxNav = HTML::select(".webDesignerPage .webMenu .".$section)->item(0);
	$page->setStaticNav($boxNav, $ref, "webContainer", $targetgroup, "webNavItems", $display = "none");
	
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