<?php
//#section#[header]
// Module Declaration
$moduleID = 192;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;
use \DEV\Profiler\status;
use \DEV\Profiler\log\errorLogger;
use \DEV\Profiler\log\publishLogger;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "platformStatus", TRUE);

$pageContainer = HTML::select(".uiMainContent")->item(0);

// Header
$title = moduleLiteral::get($moduleID, "title");
$hd = HTML::select("h2.title")->item(0);
DOM::append($hd, $title);

// Platform Status
$pStatus = new status();
$status = $pStatus->getStatus();
$sub = HTML::select("h3.statusTitle")->item(0);
if ($status['code'] == status::STATUS_OK)
{
	HTML::addClass($sub, "healthy");
	$statusDescription = moduleLiteral::get($moduleID, "lbl_healthyPlatform");
}
else
{
	HTML::addClass($sub, "error");
	$statusDescription = DOM::create("span", $status['description']);
}
DOM::append($sub, $statusDescription);

// Projects
$projectsContainer = HTML::select(".projects")->item(0);
$projects = $pStatus->getProjects();
foreach ($projects as $projectName => $projectVersions)
{
	$projectTitle = DOM::create("h4", $projectName);
	$projectRow = DOM::create("div", $projectTitle, "", "pRow");
	DOM::append($projectsContainer, $projectRow);
	
	// Publish Version
	$version = DOM::create("p", "Version Published: ".$projectVersions['publish']);
	DOM::append($projectRow, $version);
	
	if (privileges::accountToGroup("RB_DEVELOPER"))
	{
		$version = DOM::create("p", "Version Deployed: ".$projectVersions['deploy']);
		DOM::append($projectRow, $version);
	}
}

// Dates
$datesContainer = HTML::select(".dates")->item(0);
$pLogger = new publishLogger(publishLogger::PUBLISH);
$logs = $pLogger->getLogs();
$logs = array_reverse($logs);
$latest_log = $logs[0];

$pubDate = DOM::create("h4", "Latest Publish at: ".date("d-m-Y H:i:s", $latest_log['time']));
DOM::append($datesContainer, $pubDate);

if (privileges::accountToGroup("RB_DEVELOPER"))
{
	$pLogger = new publishLogger(publishLogger::DEPLOY);
	$logs = $pLogger->getLogs();
	$logs = array_reverse($logs);
	$latest_log = $logs[0];
	
	$pubDate = DOM::create("h4", "Latest Deploy at: ".date("d-m-Y H:i:s", $latest_log['time']));
	DOM::append($datesContainer, $pubDate);
}


if (privileges::accountToGroup("RB_DEVELOPER"))
{
	$errorLogsContainer = DOM::create("div", "", "", "errorLogs");
	DOM::append($pageContainer, $errorLogsContainer);
	
	$erLogger = new errorLogger();
	$logs = $erLogger->getLogs();
	
	$title = DOM::create("h4", "Platform Errors (".count($logs).")");
	DOM::append($errorLogsContainer, $title);
	
	foreach ($logs as $log)
	{
		// Create entry box
		$box = DOM::create("div", "", "", "logEntry");
		DOM::append($errorLogsContainer, $box);
		
		$timespan = DOM::create("span", date("d-m-Y H:i:s", $log['time']), "", "logTime");
		DOM::append($box, $timespan);
		
		$logContent = DOM::create("pre", $log['description'], "", "logContent");
		DOM::append($box, $logContent);
	}
}


// Return output
return $page->getReport();
//#section_end#
?>