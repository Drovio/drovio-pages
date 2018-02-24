<?php
//#section#[header]
// Module Declaration
$moduleID = 33;

// Inner Module Codes
$innerModules = array();
$innerModules['pages'] = 338;
$innerModules['security'] = 50;
$innerModules['market'] = 275;
$innerModules['overview'] = 277;
$innerModules['settings'] = 314;
$innerModules['cdn'] = 346;

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
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "adminHomePage", TRUE, TRUE);
$sectionContainer = HTML::select(".adminHomePage .adminContent")->item(0);

// Get selected tab
$selectedTab = engine::getVar('tab');
$selectedTab = empty($selectedTab) ? "overview" : $selectedTab;
$boxNav = HTML::select(".adminHomePage .adminMenu .".$selectedTab)->item(0);
HTML::addClass($boxNav, "selected");

// Set Sidebar sections
$sections = array();
$sections[] = "overview";
$sections[] = "pages";
$sections[] = "cdn";
$sections[] = "settings";
$sections[] = "security";
$sections[] = "market";
foreach ($sections as $section)
{
	if (!isset($innerModules[$section]))
		continue;

	// Set url
	$url = url::resolve("admin", "/".$section."/");
	$box = HTML::select(".adminHomePage .adminMenu .".$section." a")->item(0);
	DOM::attr($box, "href", $url);
	
	// Set static navigation
	$ref = "admin_".$section;
	$targetgroup = "admin_section_group";
	$boxNav = HTML::select(".adminHomePage .adminMenu .".$section)->item(0);
	$page->setStaticNav($boxNav, $ref, "adminContainer", $targetgroup, "admNavItems", $display = "none");
	
	// Set data-ref
	HTML::data($boxNav, "ref", $section);
	
	// Add module container
	$attr = array();
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