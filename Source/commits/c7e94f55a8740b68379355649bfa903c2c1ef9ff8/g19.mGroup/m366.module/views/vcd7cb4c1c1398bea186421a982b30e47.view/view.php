<?php
//#section#[header]
// Module Declaration
$moduleID = 366;

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
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\team;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "drovioSubDialog", TRUE);

// Get account teams
$teamContainer = HTML::select(".drovioSub .teams")->item(0);
$teams = team::getAccountTeams();
if (!empty($teams))
	HTML::innerHTML($teamContainer, "");
foreach ($teams as $index => $teamInfo)
{
	// Check team uname
	if (empty($teamInfo['uname']))
	{
		unset($teams[$index]);
		continue;
	}
		
	// Create weblink
	$href = url::resolve($teamInfo['uname'], "/");
	$ttile = $pageContent->getWeblink($href, $content = "", $target = "_self", $mID = "", $viewName = "", $attr = array(), $class = "ttile");
	DOM::append($teamContainer, $ttile);
	
	// Create team icon
	$img = NULL;
	if (!empty($teamInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $teamInfo['profile_image_url']);
	}
	$icon = DOM::create("div", $img, "", "icon");
	DOM::append($ttile, $icon);
	
	$title = DOM::create("div", $teamInfo['name'], "", "title");
	DOM::append($ttile, $title);
}

// Create new team action
$newTeam = HTML::select(".navitem.new")->item(0);
$actionFactory->setModuleAction($newTeam, $moduleID, "createTeam");

// Return output
return $pageContent->getReport();
//#section_end#
?>