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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\misc\platformStatus;
use \API\Developer\misc\dbSync;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;

// Build the page content
$pageContent = new HTMLContent();
$pageContent->build("dbSyncResult");

dbSync::loadSchemas();
$result = dbSync::checkSchemas();

// Log date
$pStatus = new platformStatus();
$pStatus->setDate("checkDb", time());

if (empty($result['upload']) && empty($result['delete']))
{
	// Return success status
	$status = DOM::create("span", "SUCCESS", "", "success");
	$pageContent->buildElement($status);
	
	// Add action to proceed to step 2
	$pageContent->addReportAction("nextStep.publisher", $value = "2");
	
	return $pageContent->getReport("#dbSchemaStatus");
}

// Return error status
$status = DOM::create("span", "ERROR", "", "error");
$pageContent->addReportContent($status, $holder = "#dbSchemaStatus", $method = "replace");


if (!empty($result['upload']))
{
	$upload = DOM::create("div", "", "", "upload");
	$pageContent->append($upload);
	
	$title = DOM::create("p", "Upload to publish server:");
	DOM::append($upload, $title);
	
	$context = DOM::create("span", print_r($result['upload'], TRUE));
	DOM::append($upload, $context);
}

if (!empty($result['delete']))
{
	$delete = DOM::create("div", "", "", "delete");
	$pageContent->append($delete);
	
	$title = DOM::create("p", "Delete from publish server:");
	DOM::append($delete, $title);
	
	$context = DOM::create("span", print_r($result['delete'], TRUE));
	DOM::append($delete, $context);
}



// Return content report
return $pageContent->getReport("#dbSchemaContext");
//#section_end#
?>