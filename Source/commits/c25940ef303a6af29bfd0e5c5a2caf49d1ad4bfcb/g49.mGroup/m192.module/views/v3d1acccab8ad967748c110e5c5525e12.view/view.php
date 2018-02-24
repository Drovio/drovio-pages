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
$hd = HTML::select("h1.title")->item(0);
DOM::append($hd, $title);

// Platform Status
$pStatus = new status();
$status = $pStatus->getStatus();
$sub = HTML::select("h2.statusTitle")->item(0);
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

// Get project statuses
$projects = $pStatus->getProjects();
$projectsContainer = HTML::select(".projects")->item(0);


// Publish status
$header = DOM::create("h3", "Redback Publish Information");
DOM::append($projectsContainer, $header);

foreach ($projects as $projectName => $projectVersions)
	if (isset($projectVersions['publish']))
	{
		$projectRow = DOM::create("div", "", "", "pRow");
		DOM::append($projectsContainer, $projectRow);
			
		$version = DOM::create("p", $projectName." Publish Version: ".$projectVersions['publish']);
		DOM::append($projectRow, $version);
	}
	
// Last deploy date
$pLogger = new publishLogger(publishLogger::PUBLISH);
$logs = $pLogger->getLogs();
$logs = array_reverse($logs);
$latest_log = $logs[0];

$pubDate = DOM::create("h4", "Latest Publish at: ".date("d-m-Y H:i:s", $latest_log['time']));
DOM::append($projectsContainer, $pubDate);



// Deploy and Error logs
if (privileges::accountToGroup("RB_DEVELOPER"))
{
	$header = DOM::create("h3", "Redback Deploy Information");
	DOM::append($projectsContainer, $header);
	
	foreach ($projects as $projectName => $projectVersions)
		if (isset($projectVersions['deploy']))
		{
			$projectRow = DOM::create("div", "", "", "pRow");
			DOM::append($projectsContainer, $projectRow);
			
			$version = DOM::create("p", $projectName." Deploy Version: ".$projectVersions['deploy']);
			DOM::append($projectRow, $version);
		}
	
	// Last deploy date
	$pLogger = new publishLogger(publishLogger::DEPLOY);
	$logs = $pLogger->getLogs();
	$logs = array_reverse($logs);
	$latest_log = $logs[0];
	
	$pubDate = DOM::create("h4", "Latest Deploy at: ".date("d-m-Y H:i:s", $latest_log['time']));
	DOM::append($projectsContainer, $pubDate);
	
	
	// Error Logs
	$errorLogsContainer = DOM::create("div", "", "", "errorLogs");
	DOM::append($projectsContainer, $errorLogsContainer);
	
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