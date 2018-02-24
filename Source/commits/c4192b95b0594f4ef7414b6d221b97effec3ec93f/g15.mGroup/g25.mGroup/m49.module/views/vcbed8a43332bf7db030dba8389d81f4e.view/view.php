<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \UI\Html\HTMLContent;
use \UI\Presentation\notification;
use \UI\Presentation\dataGridList;
use \DEV\Core\sql\sqlQuery;

// Create Module Page
$content = new HTMLContent();

// Build the module
$content->build("databaseTestingResult", "databaseResult");

// Execute the query
$dbc = new interDbConnection();
$qid = str_replace('q_', '', $_GET['qid']);
$dbq = new sqlQuery($_GET['domain'], $qid);

// Create Report Notification
$reportNtf = new notification();

try
{
	$result = $dbc->execute($dbq);
	
	if (is_bool($result))
	{
		// Get result status for header
		$header = DOM::create("h4", "The query returned ".($result ? "TRUE" : "FALSE"));
	}
	else
	{
		// Get row count for header
		$rowCount = $dbc->get_num_rows($result);
		$header = DOM::create("h4", "The query returned ".$rowCount." results.");
	}
	
	// Create result title
	$resultTitle = DOM::create("div", "", "", "resultTitle");
	DOM::append($resultTitle, $header);
	$content->append($resultTitle);
	
	if ($rowCount > 0)
	{
		// Create Result Container
		$container = DOM::create("div", "", "", "resultContainer");
		$content->append($container);
		
		// Build Data Grid List
		$gridList = new dataGridList();
		$resultGrid = $gridList->build($id = "resultGrid", $checkable = FALSE)->get();
		DOM::append($container, $resultGrid);
		
		// Fetch All Data
		$resultData = $dbc->fetch($result, TRUE);
		
		// Set Headers
		if (is_array($resultData[0]))
			$keys = array_keys($resultData[0]);
		$gridList->setHeaders($keys);
		
		foreach ($resultData as $resultRow)
			$gridList->insertRow($resultRow);
		
	}
}
catch (Exception $ex)
{
	// Create Notification
	$notification = $reportNtf->build($type = "error", $header = TRUE, $footer = FALSE)->get();
	$content->append($notification);
	
	// Set Custom Message
	$reportMessage = $reportNtf->getMessage("error", "err.execute_error");
	$reportNtf->append($reportMessage);
	$reportNtf->appendCustomMessage("SQL Error: ".$ex->getMessage());
}

// Append Test Content
$content->append($testContent);



// Return output
$poolClass = "testingPool_".$_GET['domain']."_".$_GET['qid'];
$poolClass = str_replace(".", "_", $poolClass);
return $content->getReport(".".$poolClass." .testResult");
//#section_end#
?>