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
use \DEV\Projects\project;
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



// Add project ids to list
$projectIDs = array();
$projectIDs[] = 1;
$projectIDs[] = 2;

// Set released versions
$pinfoBoxContainer = HTML::select(".pinfoBoxContainer")->item(0);
foreach ($projectIDs as $projectID)
{
	/*
	// Get last release version
	$lastVersion = projectLibrary::getLastProjectVersion($projectID, $live = FALSE);
	if (empty($lastVersion))
		continue;
	*/
	
	// Create infobox
	$infoBox = DOM::create("div", "", "", "infobox");
	DOM::append($pinfoBoxContainer, $infoBox);
	
	// Add project information
	$pinfo = DOM::create("div", "", "", "pinfo");
	DOM::append($infoBox, $pinfo);
	
	// Get project info
	$project = new project($projectID);
	$projectInfo = $project->info();
	
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectInfo['icon_url']);
	$picon = DOM::create("div", $img, "", "picon");
	DOM::append($pinfo, $picon);
	
	$ptitle = DOM::create("div", $projectInfo['title'], "", "ptitle");
	DOM::append($pinfo, $ptitle);
	
	// Get last 5 releases
	$releases = $project->getReleases();
	$last5Releases = array_slice($releases, 0, 5);
	foreach ($last5Releases as $releaseInfo)
	{
		$relInfoRow = getReleaseRow($releaseInfo);
		DOM::append($infoBox, $relInfoRow);
	}
	
	// Get another 10 as hidden
	$last10Releases = array_slice($releases, 5, 10);
	$moreReleases = DOM::create("div", "", "", "more_releases");
	DOM::append($infoBox, $moreReleases);
	foreach ($last10Releases as $releaseInfo)
	{
		$relInfoRow = getReleaseRow($releaseInfo);
		DOM::append($moreReleases, $relInfoRow);
	}
	
	// Add more button
	$more = DOM::create("div", "", "", "more");
	DOM::append($infoBox, $more);
}

// Return output
return $page->getReport();

function getReleaseRow($releaseInfo)
{
	$relInfo = DOM::create("div", "", "", "relInfo");

	// Version
	$bver = DOM::create("b", " ".$releaseInfo['version']);
	$relVersion = DOM::create("div", "Version ", "", "rel_version");
	DOM::append($relVersion, $bver);
	DOM::append($relInfo, $relVersion);

	// Time created
	$live = datetimer::live($releaseInfo['time_created'], 'd F, Y');
	$relTime = DOM::create("div", $live, "", "rel_time");
	DOM::append($relInfo, $relTime);

	// Changelog
	$relChangelog = DOM::create("div", $releaseInfo['changelog'], "", "rel_changelog");
	DOM::append($relInfo, $relChangelog);
	
	return $relInfo;
}
//#section_end#
?>