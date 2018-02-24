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
use \API\Resources\geoloc\datetimer;
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

// Set platform status scope class
$rbDeveloper = privileges::accountToGroup("RB_DEVELOPER");
if ($rbDeveloper)
{
	$mainContainer = HTML::select("#platformStatus")->item(0);
	HTML::addClass($mainContainer, "inner");
}


$statusBarTitle = HTML::select(".sectionHeader")->item(0);
$statusTitle = moduleLiteral::get($moduleID, "lbl_statusHeader");
$title = DOM::create("h4", $statusTitle);
DOM::append($statusBarTitle, $title);

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


// Release Info
$deployHeader = HTML::select(".sectionHeader")->item(1);
$title = moduleLiteral::get($moduleID, "lbl_deployHeader");
$header = DOM::create("h4", $title);
DOM::append($deployHeader, $header);

$publisherHeader = HTML::select(".sectionHeader")->item(2);
$title = moduleLiteral::get($moduleID, "lbl_publisherHeader");
$header = DOM::create("h4", $title);
DOM::append($publisherHeader, $header);

$issuesHeader = HTML::select(".sectionHeader")->item(3);
$title = moduleLiteral::get($moduleID, "lbl_issuesHeader");
$header = DOM::create("h4", $title);
DOM::append($issuesHeader, $header);

// Get project statuses
$projects = $pStatus->getProjects();

// Deploy
if ($rbDeveloper)
{
	$deployContainer = HTML::select(".depInfo .content")->item(0);
	foreach ($projects as $projectName => $projectVersions)
		if (isset($projectVersions['deploy']))
		{
			$projectRow = DOM::create("div", "", "", "pRow");
			DOM::append($deployContainer, $projectRow);
			
			$pName = DOM::create("div", $projectName, "", "pName");
			DOM::append($projectRow, $pName);
				
			$pVersion = DOM::create("div", "v".$projectVersions['deploy'], "", "pVer");
			DOM::append($projectRow, $pVersion);
		}
		
	// Last publish date
	$pLogger = new publishLogger(publishLogger::DEPLOY);
	$logs = $pLogger->getLogs();
	$logs = array_reverse($logs);
	$latest_log = $logs[0];
	
	$dateTitle = moduleLiteral::get($moduleID, "lbl_lastDeployDate");
	$tDate = DOM::create("div", $dateTitle, "", "tDate");
	$dateLive = datetimer::live($latest_log['time']);
	DOM::append($tDate, $dateLive);
	DOM::append($deployContainer, $tDate);
}


// Publish status
$publishContainer = HTML::select(".pubInfo .content")->item(0);
foreach ($projects as $projectName => $projectVersions)
	if (isset($projectVersions['publish']))
	{
		$projectRow = DOM::create("div", "", "", "pRow");
		DOM::append($publishContainer, $projectRow);
		
		$pName = DOM::create("div", $projectName, "", "pName");
		DOM::append($projectRow, $pName);
			
		$pVersion = DOM::create("div", "v".$projectVersions['publish'], "", "pVer");
		DOM::append($projectRow, $pVersion);
	}
	
// Last publish date
$pLogger = new publishLogger(publishLogger::PUBLISH);
$logs = $pLogger->getLogs();
$logs = array_reverse($logs);
$latest_log = $logs[0];

$dateTitle = moduleLiteral::get($moduleID, "lbl_lastPublishDate");
$tDate = DOM::create("div", $dateTitle, "", "tDate");
$dateLive = datetimer::live($latest_log['time']);
DOM::append($tDate, $dateLive);
DOM::append($publishContainer, $tDate);


// Issue History
if ($rbDeveloper)
{
	$errorLogsContainer = HTML::select(".issues .content")->item(0);
	
	$erLogger = new errorLogger();
	$logs = $erLogger->getLogs();
	
	if (count($logs) == 0)
	{
		$title = moduleLiteral::get($moduleID, "lbl_noIssues");
		$header = DOM::create("h3", $title);
		DOM::append($errorLogsContainer, $header);
	}
	else
		foreach ($logs as $log)
		{
			// Create entry box
			$box = DOM::create("div", "", "", "logEntry");
			DOM::append($errorLogsContainer, $box);
			
			$timespan = DOM::create("span", date("H:i:s", $log['time']), "", "logTime");
			DOM::append($box, $timespan);
			
			$logContent = DOM::create("pre", $log['description'], "", "logContent");
			DOM::append($box, $logContent);
		}
}	

// Remove deploy container and issue history
if (!$rbDeveloper)
{
	$deployContainer = HTML::select(".depInfo")->item(0);
	DOM::replace($deployContainer, NULL);
	
	$issuesContainer = HTML::select(".issues")->item(0);
	DOM::replace($issuesContainer, NULL);
}

// Return output
return $page->getReport();
//#section_end#
?>