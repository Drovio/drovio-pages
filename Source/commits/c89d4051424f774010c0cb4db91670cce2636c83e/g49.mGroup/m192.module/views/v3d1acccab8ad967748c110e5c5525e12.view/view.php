<?php
//#section#[header]
// Module Declaration
$moduleID = 192;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \API\Security\privileges;
use \UI\Modules\MPage;
use \DEV\Profiler\status;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "platformStatus", TRUE);

// Platform Status
$pStatus = new status();
$status = $pStatus->getStatus();
$statusBar = HTML::select(".statusBar")->item(0);
$statusContent = HTML::select(".statusBar .content")->item(0);
if ($status['code'] == status::STATUS_OK)
{
	HTML::addClass($statusBar, "healthy");
	$desc = moduleLiteral::get($moduleID, "lbl_healthyPlatform");
	$statusDescription = DOM::create("h3", $desc);
}
else
{
	HTML::addClass($statusBar, "sick");
	$statusDescription = DOM::create("h3", $status['description']);
}
DOM::append($statusContent, $statusDescription);

// Get project statuses
$pids = array();
$pids[] = 1;
$pids[] = 2;
$pids[] = 3;

// Set released versions
$publishContainer = HTML::select(".pubInfo .content")->item(0);
foreach ($pids as $pid)
{
	$devProject = new project($pid);
	$pInfo = $devProject->info();
	$projectTitle = $pInfo['title'];
	
	$releases = $devProject->getReleases();
	$latest_release = $releases[0];
	if (empty($latest_release))
		continue;
	
	$projectRow = DOM::create("div", "", "", "pRow");
	DOM::append($publishContainer, $projectRow);
	
	$pName = DOM::create("div", $projectTitle, "", "pName");
	DOM::append($projectRow, $pName);
	
	if (!empty($latest_release['version']))
	{
		$pVersion = DOM::create("div", "v".$latest_release['version'], "", "pVer");
		DOM::append($projectRow, $pVersion);
	}
	
	// Release date
	$live = datetimer::live($latest_release['time_created']);
	$pTime = DOM::create("div", $releaseTitle, "", "pTime");
	$relDateTitle = moduleLiteral::get($moduleID, "lbl_projectLastRelease", $attr);
	DOM::append($pTime, $relDateTitle);
	$live = datetimer::live($latest_release['time_created']);
	DOM::append($pTime, $live);
	DOM::append($projectRow, $pTime);
}

// Return output
return $page->getReport();
//#section_end#
?>