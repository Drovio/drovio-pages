<?php
//#section#[header]
// Module Declaration
$moduleID = 88;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Html");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\team;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "bossHomePage", TRUE);

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($moduleID, "navigationBar");
DOM::append($navBar, $navigationBar);


// Check team existence
$teamID = team::getTeamID();
if (empty($teamID))
{
	// Remove visit link
	$sneakPeek = HTML::select(".sneak_peek")->item(0);
	HTML::innerHTML($sneakPeek, "");
	
	// Add "Create team link"
	$title = moduleLiteral::get($moduleID, "lbl_createTeam");
	$href = url::resolve("my", "/relations/");
	$createTeam = $page->getWeblink($href, $title, "_blank");
	DOM::append($sneakPeek, $createTeam);
}


// Load footer menu
$frontendPage = HTML::select(".bossPage")->item(0);
$footerMenu = module::loadView($moduleID, "footerMenu");
DOM::append($frontendPage, $footerMenu);

return $page->getReport();
//#section_end#
?>