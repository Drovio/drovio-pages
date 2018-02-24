<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Html");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLContent;
use \DEV\Apps\appManager;

// Build Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "appList", TRUE);


// Get published applications
$apps = appManager::getAppCenterApps();
foreach ($apps as $app)
{
	// Create app Tile
	$appTile = DOM::create("div", "", "", "appTile");
	$pageContent->append($appTile);
	
	// Set Icon
	$icon = DOM::create("div", "", "", "appIco");
	DOM::append($appTile, $icon);
	
	// App desc
	$appContext = DOM::create("div", "", "", "appContext");
	DOM::append($appTile, $appContext);
	
	// Set title
	$href = url::resolve("apps", "/application.php");
	$params = array();
	$params['id'] = $app['id'];
	$url = url::get($href, $params);
	$wl = $pageContent->getWeblink($url, $app['title'], "_blank");
	$title = DOM::create("h2", $wl, "", "appTitle");
	DOM::append($appContext, $title);
	
	// Set description
	$desc = DOM::create("p", $app['description'], "", "appDesc");
	DOM::append($appContext, $desc);
}

if (count($apps) == 0)
{
	$title = moduleLiteral::get($moduleID, "lbl_noApps");
	$noApps = DOM::create("h3", $title, "", "noApps");
	$pageContent->append($noApps);
}

return $pageContent->getReport(".appContainer .apps");
//#section_end#
?>