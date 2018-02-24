<?php
//#section#[header]
// Module Declaration
$moduleID = 376;

// Inner Module Codes
$innerModules = array();
$innerModules['info'] = 377;

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
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get team information
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');

// Get team public information
$dbc = new dbConnection();
$q = $page->getQuery("get_team_info");
$attr = array();
$attr['id'] = $teamID;
$attr['name'] = $teamName;
$result = $dbc->execute($q, $attr);
$teamInfo = $dbc->fetch($result);
$teamID = $teamInfo['id'];
$teamInfo = team::info($teamID);
if (empty($teamName) && !empty($teamInfo['uname']))
{
	// Redirect to friendly url
	$url = url::resolve("www", "/profile/".$teamInfo['uname']);
	return $actionFactory->getReportRedirect($url, "", $formSubmit = TRUE);
}

// Get team name
$teamName = $teamInfo['name'];

// Build the module content
$page->build($teamName, "teamProfilePage", TRUE);

// Set team name
$teamNameContainer = HTML::select(".teamName")->item(0);
HTML::innerHTML($teamNameContainer, $teamName);

// Set team profile picture
if (isset($teamInfo['profile_image_url']))
{
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	
	// Append to logo
	$logo = HTML::select(".teamProfile .logoBox .logo")->item(0);
	DOM::append($logo, $img);
}

// Set navigation
$items = array();
$items['info'] = "teamInfo";
foreach ($items as $class => $ref)
{
	// Set nav item
	$item = HTML::select(".teamNavigation .navitem.".$class)->item(0);
	$page->setStaticNav($item, $ref, "teamDetailsContainer", "teamGroup", "teamNavGroup", $display = "none");
	
	// Avoid empty modules
	if (empty($innerModules[$class]))
		continue;
	
	// Get module container
	$teamDetailsContainer = HTML::select(".teamDetailsContainer")->item(0);
	$attr = array();
	$attr['id'] = $teamID;
	$attr['name'] = $teamName;
	$mContainer = $page->getModuleContainer($innerModules[$class], $viewName = "", $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload = TRUE);
	$page->setNavigationGroup($mContainer, "teamGroup");
	HTML::append($teamDetailsContainer, $mContainer);
}

// Return output
return $page->getReport();
//#section_end#
?>