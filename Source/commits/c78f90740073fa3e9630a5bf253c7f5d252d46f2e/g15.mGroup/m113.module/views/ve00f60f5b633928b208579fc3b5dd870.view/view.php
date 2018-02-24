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
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Developer\misc\dbSync;
use \UI\Html\HTMLContent;

// Build the page content
$pageContent = new HTMLContent();
$pageContent->build("dbSyncResult");

// Sync database data
$status1 = dbSync::uploadSystemData();
$status2 = dbSync::downloadUserData();

if (is_bool($status1) && is_bool($status2))
	$status = ($status1 && $status2);
else
	$status = $status1."\n".$status2;

if ($status === TRUE)
{
	// Return success status
	$statusText = DOM::create("span", "SUCCESS", "", "success");
	$pageContent->buildElement($statusText);
	
	// Add action to proceed to step 2
	$pageContent->addReportAction("nextStep.publisher", $value = "3");
	
	return $pageContent->getReport("#dbDataStatus");
}
else
{
	// Return error status
	$statusText = DOM::create("span", "ERROR", "", "error");
	$pageContent->addReportContent($statusText, $holder = "#dbDataStatus", $method = "replace");
	
	$errorText = DOM::create("span", "Data synchronization failed. Please try again.\nDetails:\n");
	$pageContent->append($errorText);
	
	$statusText = DOM::create("span", $status);
	$pageContent->append($statusText);
	
	return $pageContent->getReport("#dbDataContext");
}



// Return content report
return $pageContent->getReport("#dbDataContext");
//#section_end#
?>