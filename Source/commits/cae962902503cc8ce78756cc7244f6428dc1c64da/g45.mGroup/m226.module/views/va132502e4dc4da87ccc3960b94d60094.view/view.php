<?php
//#section#[header]
// Module Declaration
$moduleID = 226;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
importer::import("BSS", "Dashboard");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \UI\Modules\MPage;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;
use \BSS\Dashboard\dashboard;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "bossDashboardPage", TRUE);

// Dashboard link
$title = moduleLiteral::get($moduleID, "lbl_dashboard", array(), FALSE);
$page->addToolbarNavItem("bsDashboard", $title, "dashboard", NULL);

// Active applications
$activeAppsTitle = moduleLiteral::get($moduleID, "lbl_activeApps", array(), FALSE);
$collection = $page->getRCollection("activeAppsContainer", $activeAppsTitle);
$title = moduleLiteral::get($moduleID, "lbl_apps");
$page->addToolbarNavItem("bsApps", $title, "activeApps", $collection, $ribbonType = "inline", $type = "obedient");


// Get dashboard manager
$dashManager = dashboard::getInstance();


// Set team name
$tName = HTML::select(".dashInfo .title")->item(0);
HTML::nodeValue($tName, team::getTeamName());

// Load and add applications to grid
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_apps");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$apps = $dbc->fetch($result, TRUE);

// Add applications to dashboard
if (count($apps) > 0)
{
	// Remove noApps container
	$noApps = HTML::select(".noApps")->item(0);
	HTML::replace($noApps, NULL);
	
	// Get application position from dashboard
	$dashboard = dashboard::getInstance();
	$dbApps = $dashboard->getApplications();
	
	// Get num slides from dashboard preferences
	//$numSlides = $dashManager->getSlidesCount();
	$numSlides = ceil(count($apps) / 32);
	$slideContainer = HTML::select(".slideContainer .slides")->item(0);
	for ($i=0; $i<$numSlides; $i++)
	{
		$slide = DOM::create("div", "", "", "slide");
		DOM::append($slideContainer, $slide);
		
		$grid = DOM::create("div", "", "", "grid");
		DOM::append($slide, $grid);
	}
	
	// Get active from dashboard, for now set the first
	$active = 0;
	$activeSlide = HTML::select(".slideContainer .slide")->item($active);
	HTML::addClass($activeSlide, "active");
	
	// Read preferences from the dashboard and load application grid
	$counter = 0;
	foreach ($apps as $app)
	{
		// Read dashboard preferences and get app box
		$appSlide = floor($counter / 32);
		$appsGrid = HTML::select(".apps_grid .slide .grid")->item($appSlide);
		
		// Get application ico
		$appIcon = projectLibrary::getPublishedPath($app['project_id'], $app['version'])."/resources/ico.png";
		if (file_exists(systemRoot.$appIcon))
		{
			$appTileIcon = str_replace(paths::getPublishedPath(), "", $appIcon);
			$appTileIcon = url::resolve("lib", $appTileIcon);
		}
		
		// Set temp applications in stack (in line)
		$posX = $counter % 8;
		$posY = floor($counter / 8);
		
		// Set grid box
		$gridBox = getAppBox($app['title'], $size = 1, $posX, $posY, $appTileIcon);
		HTML::append($appsGrid, $gridBox['grid']);
		
		// Set application attributes
		$appBox = $gridBox['app'];
		$applicationData = array();
		$applicationData['id'] = $app['project_id'];
		HTML::data($appBox, "app", $applicationData);
		
		$counter++;
	}
	
	// Set application listeners
	$apps = HTML::select(".grid .app");
	foreach ($apps as $app)
		$actionFactory->setModuleAction($app, $innerModules['appPlayer'], "", "", $attr);
	
	// Set navigation
	setNavBalls();
}


// Settings dialog

// Themes
$themes = array();
$themes[] = "th01";
$themes[] = "th02";
$themes[] = "th03";
$themes[] = "th04";
$themes[] = "th05";
$themes[] = "th06";
$themesContainer = HTML::select(".settings_popup .themes")->item(0);
foreach ($themes as $thm)
{
	$thm_tile = DOM::create("span", "", "", "thm_tile");
	HTML::addClass($thm_tile, $thm);
	
	HTML::attr($thm_tile, "data-thm", $thm);
	
	DOM::append($themesContainer, $thm_tile);
}

// Colors
$colors = array();
$colors[] = "transparent";
$colors[] = "#017981";
$colors[] = "#ed1c24";
$colors[] = "#ffde17";
$colors[] = "#8dc63f";
$colors[] = "#AD2E75";
$colors[] = "#D94F00";
$colors[] = "#618BFF";
$colors[] = "#3E4720";
$colorContainer = HTML::select(".settings_popup .colors")->item(0);
foreach ($colors as $clr)
{
	$clr_tile = DOM::create("span", "", "", "clr_tile");
	HTML::attr($clr_tile, "style", "background: ".$clr);
	
	HTML::attr($clr_tile, "data-clr", $clr);
	
	DOM::append($colorContainer, $clr_tile);
}


// Return output
return $page->getReport();

function setNavBalls()
{
	// Get number of slides
	$numSlides = HTML::select(".slideContainer .slide")->length;
	$navBallContainer = HTML::select(".navBar .navBalls")->item(0);
	for ($i=0; $i<$numSlides; $i++)
	{
		$navBall = HTML::create("li", "", "", "navBall");
		HTML::append($navBallContainer, $navBall);
	}
	
	// Get active from dashboard, for now set the first
	$active = 0;
	$activeNavBall = HTML::select(".navBalls .navBall")->item($active);
	HTML::addClass($activeNavBall, "active");
}


function getAppBox($title, $size = 1, $posX = 0, $posY = 0, $appTileIcon = "")
{
	$size = ($size < 1 ? 1 : $size);
	
	$gridBox = DOM::create("div", "", "", "gb");
	// Set grid size
	HTML::addClass($gridBox, "s".$size);
	// Set app position
	HTML::addClass($gridBox, "pos".$posX.$posY);
	
	// Add data for reading
	//DOM::attr($gridBox, "data-size", $size);
	$position = array();
	$position['x'] = $posX;
	$position['y'] = $posY;
	DOM::data($gridBox, "position", $position);
	
	$appContainer = DOM::create("div", "", "", "appContainer");
	DOM::append($gridBox, $appContainer);
	
	$appBox = DOM::create("div", "", "", "app");
	DOM::append($appContainer, $appBox);
	
	// Application Icon
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($appBox, $ico);
	// Set ico image
	if (!empty($appTileIcon))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $appTileIcon);
		DOM::append($ico, $img);
	}
	
	// Application title
	$t = DOM::create("span", $title, "", "title");
	DOM::append($appBox, $t);
	/*
	$size = DOM::create("span", ($size == 1 ? ">" : "<"), "", "size");
	DOM::append($appBox, $size);
	*/
	$box = array();
	$box['grid'] = $gridBox;
	$box['app'] = $appBox;
	return $box;
}
//#section_end#
?>