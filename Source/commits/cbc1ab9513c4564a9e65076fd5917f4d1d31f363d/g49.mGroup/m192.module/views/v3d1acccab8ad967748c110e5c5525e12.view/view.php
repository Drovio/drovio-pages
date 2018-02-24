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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \API\Model\modules\module;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "platformStatusPage", TRUE);

// Get project statuses
$pids = array();
$pids[] = 1;
$pids[] = 2;
$pids[] = 3;

// Set released versions
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_project_releases");
$publishContainer = HTML::select(".framework .content")->item(0);
foreach ($pids as $pid)
{
	// Get last release
	$attr = array();
	$attr['pid'] = $pid;
	$result = $dbc->execute($q, $attr);
	$latest_release = $dbc->fetch($result);
	if (empty($latest_release))
		continue;
	
	$projectRow = DOM::create("div", "", "", "pRow");
	DOM::append($publishContainer, $projectRow);
	
	$projectTitle = $latest_release['title'];
	$pName = DOM::create("div", $projectTitle, "", "pName");
	DOM::append($projectRow, $pName);
	
	$pIco = DOM::create("div", "", "", "pIco healthy");
	DOM::append($projectRow, $pIco);
	
	// Version
	if (!empty($latest_release['version']))
	{
		$pVersion = DOM::create("div", "", "", "pVer");
		DOM::append($projectRow, $pVersion);
		
		$version = DOM::create("span", "v".$latest_release['version'], "", "version");
		DOM::append($pVersion, $version);
		
		// Release date
		$live = datetimer::live($latest_release['time_created']);
		$pTime = DOM::create("div", $releaseTitle, "", "time");
		$relDateTitle = moduleLiteral::get($moduleID, "lbl_projectLastRelease", $attr);
		DOM::append($pTime, $relDateTitle);
		$live = datetimer::live($latest_release['time_created']);
		DOM::append($pTime, $live);
		DOM::append($pVersion, $pTime);
	}
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$discoverPage = HTML::select(".platformStatus")->item(0);
$footerMenu = module::loadView($innerModules['devHome'], "footerMenu");
DOM::append($discoverPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>