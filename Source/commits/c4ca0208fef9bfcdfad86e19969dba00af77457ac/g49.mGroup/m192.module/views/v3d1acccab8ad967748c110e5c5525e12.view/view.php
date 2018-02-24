<?php
//#section#[header]
// Module Declaration
$moduleID = 192;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \UI\Modules\MPage;
use \DEV\Projects\projectLibrary;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "platformStatusPage dev-domain", TRUE, TRUE);
$sidebarContainer = HTML::select(".platformStatus .dev-sidebar")->item(0);

// Load navigation bar on mainpage
$navBar = HTML::select(".platformStatus .dev-mainpage .navbar")->item(0);
$navigationBar = $page->loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load sidebar
$sidebar = $page->loadView($innerModules['devHome'], "sidebar");
DOM::append($sidebarContainer, $sidebar);


// Set navigation
$nav = array();
$nav["status"] = "status";
$nav["issues"] = "issues";
foreach ($nav as $class => $viewName)
{
	$ref = $class;
	$navItem = HTML::select(".platformStatus .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "statusContainer", $targetgroup = "status_mGroup", $navgroup = "mGroup", $display = "none");
	
	$mContainer = HTML::select("#".$ref)->item(0);
	$page->setNavigationGroup($mContainer, "status_mGroup");
}

// Add project ids to list
$pids = array();
$pids[] = 1;
$pids[] = 2;
$pids[] = 3;

// Set released versions
$publishContainer = HTML::select(".infoBox.framework")->item(0);
foreach ($pids as $projectID)
{
	// Get last release version
	$version = projectLibrary::getLastProjectVersion($projectID, $live = FALSE);
	if (empty($version))
		continue;
	
	// Get release info
	$releaseInfo = projectLibrary::getProjectReleaseInfo($projectID, $version);
	
	
	// Create project row
	$projectRow = DOM::create("div", "", "", "pRow");
	DOM::append($publishContainer, $projectRow);
	
	$pName = DOM::create("div", $releaseInfo['title'], "", "pName");
	DOM::append($projectRow, $pName);
	
	$pIco = DOM::create("div", "", "", "pIco healthy");
	DOM::append($projectRow, $pIco);
	
	// Version
	$pVersion = DOM::create("div", "", "", "pVer");
	DOM::append($projectRow, $pVersion);
	
	$version = DOM::create("span", "v".$releaseInfo['version'], "", "version");
	DOM::append($pVersion, $version);
	
	// Release date
	$live = datetimer::live($releaseInfo['time_created'], 'd F, Y');
	$pTime = DOM::create("div", $releaseTitle, "", "time");
	$relDateTitle = moduleLiteral::get($moduleID, "lbl_projectLastRelease", $attr);
	DOM::append($pTime, $relDateTitle);
	DOM::append($pTime, $live);
	DOM::append($pVersion, $pTime);
}

// Return output
return $page->getReport();
//#section_end#
?>