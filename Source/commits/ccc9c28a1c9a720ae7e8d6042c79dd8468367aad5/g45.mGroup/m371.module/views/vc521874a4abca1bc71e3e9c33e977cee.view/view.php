<?php
//#section#[header]
// Module Declaration
$moduleID = 371;

// Inner Module Codes
$innerModules = array();
$innerModules['imageEditor'] = 374;

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
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Modules\MContent;

// Create Module Page
$page = new MContent($moduleID);
$actionFactory = $page->getActionFactory();

// Get team information
$teamID = team::getTeamID();
$teamInfo = team::info();

// Get whether the account is team admin
$teamAdmin = accountKey::validateGroup($groupName = "TEAM_ADMIN", $context = $teamID, $type = accountKey::TEAM_KEY_TYPE);

// Build content
$page->build("", "teamSettingsViewer", TRUE);

// Set team name
$teamNameContainer = HTML::select(".teamName")->item(0);
HTML::innerHTML($teamNameContainer, $teamInfo['name']);

// Set team profile picture
if (isset($teamInfo['profile_image_url']))
{
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $teamInfo['profile_image_url']);
	
	// Append to logo
	$logo = HTML::select(".teamSettings .profileImage")->item(0);
	DOM::append($logo, $img);
}

// Set navigation
$items = array();
$items['info'] = "teamInfo";
$sectionbody = HTML::select(".sectionbody")->item(0);
foreach ($items as $class => $viewName)
{
	// Set nav item
	$ref = $class."_ref";
	$item = HTML::select(".pnavigation .navitem.".$class)->item(0);
	$page->setStaticNav($item, $ref, "sectionContainer", "teamGroup", "teamNavGroup", $display = "none");
	
	// Get module container
	$attr = array();
	$attr['id'] = $teamID;
	$attr['name'] = $teamName;
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload = TRUE);
	$page->setNavigationGroup($mContainer, "teamGroup");
	HTML::append($sectionbody, $mContainer);
}

if ($teamAdmin)
{
	// Set profile image editor action
	$editProfileImageButton = HTML::select(".teamSettings .sidebar .pr_image_editor")->item(0);
	$attr = array();
	$attr['type'] = 2;
	$actionFactory->setModuleAction($editProfileImageButton, $innerModules['imageEditor'], "", "", $attr);
}
else
{
	// Remove navitems
	$privateNavItems = HTML::select(".navitem.private");
	foreach ($privateNavItems as $item)
		HTML::replace($item, NULL);
}

// Return output
return $page->getReport();
//#section_end#
?>