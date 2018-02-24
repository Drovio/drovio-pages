<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \API\Developer\profiler\tester;
use \API\Developer\misc\platformStatus;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \UI\Html\HTMLContent;

use \DEV\Profiler\log\publishLogger;


// Create Backup
$suffix = "";
$time = intval(date("G"));
if ($time < 7)
	$time = "_night";
else if ($time < 12)
	$time = "_start";
else if ($time < 19)
	$time = "_noon";
else
	$time = "_end";

// example: 29jun2013_start
$name = strtolower(date("dMY").$time);

// Get ZipFile Name
$trunkBackupName = systemRoot.tester::getTrunk()."/release/".$name.".zip";

// Get Directory Contents
$contents = directory::getContentList(systemRoot."/", FALSE);

// Set time limit
set_time_limit(100);

// release
zipManager::create($trunkBackupName, $contents, TRUE, TRUE);


// Log activity
$logDescription = "Release package '".$name.".zip' created at ".date("F j, Y, G:i (T)");
$pl = new publishLogger(publishLogger::PUBLISH);
$pl->log($logDescription);


// Log date
$pStatus = new platformStatus();
$pStatus->setDate("publish", time());


// Build the page content
$pageContent = new HTMLContent();
$pageContent->build("dbSyncResult");


// Return success status
$status = DOM::create("span", "SUCCESS", "", "success");
$pageContent->buildElement($status);

// Add action to proceed to step 2
$pageContent->addReportAction("nextStep.publisher", $value = "4");

return $pageContent->getReport("#rbPublisherStatus");
//#section_end#
?>