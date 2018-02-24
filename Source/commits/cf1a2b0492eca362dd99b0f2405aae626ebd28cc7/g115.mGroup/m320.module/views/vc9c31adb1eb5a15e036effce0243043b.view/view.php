<?php
//#section#[header]
// Module Declaration
$moduleID = 320;

// Inner Module Codes
$innerModules = array();
$innerModules['openPage'] = 308;

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
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "openDevelopersPage", TRUE);


// Get developers count
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_open_developers");
$result = $dbc->execute($q);
$developers = $dbc->fetch($result, TRUE);
$devCount = count($developers);
// Set developers count
$attr = array();
$attr['count'] = ($devCount < 10 ? $devCount : floor($devCount / 10) * 10);
$title = moduleLiteral::get($moduleID, "lbl_dev_count", $attr);
$mainTitle = HTML::select(".list .ctitle")->item(0);
HTML::append($mainTitle, $title);


// List all developers
$devContainer = HTML::select(".openDevelopers .list .middleContainer")->item(0);
foreach ($developers as $developer)
{
	// Build a developer box
	$href = url::resolve("developer", "/profile/index.php");
	$params = array();
	$params['id'] = $developer['id'];
	$href = url::get($href, $params);
	$devBox = $page->getWeblink($href, $content = "", $target = "_blank");
	HTML::addClass($devBox, "devBox");
	HTML::append($devContainer, $devBox);
	
	// Dev icon
	$devImage = HTML::create("div", "", "", "devImg");
	HTML::append($devBox, $devImage);
	
	// Dev title
	$devTitle = HTML::create("div", $developer['title'], "", "devTitle");
	HTML::append($devBox, $devTitle);
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['openPage'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$frontendPage = HTML::select(".openDevelopers")->item(0);
$footerMenu = module::loadView($innerModules['openPage'], "footerMenu");
DOM::append($frontendPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>