<?php
//#section#[header]
// Module Declaration
$moduleID = 215;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

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
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \UI\Html\HTMLContent;
use \UI\Presentation\notification;
use \UI\Presentation\dataGridList;
use \DEV\Modules\module;

// Create Module Page
$content = new HTMLContent();

// Build the module
$content->build("databaseTestingResult", "databaseResult");

// Execute the query
$dbc = new interDbConnection();
$mID = $_GET['mid'];
$qID = $_GET['qid'];
$module = new module($mID);
$mQuery = $module->getQuery("", $qID);

// Create Report Notification
$reportNtf = new notification();

// Execute the query
$result = $dbc->execute($mQuery);
if (is_bool($result))
{
	// Get result status for header
	if ($result)
		$header = DOM::create("h4", "The query returned TRUE.");
		
	// See for error
	$error = ($result === FALSE);
}
else
{
	// Get row count for header
	$rowCount = $dbc->get_num_rows($result);
	$header = DOM::create("h4", "The query returned ".$rowCount." results.");
}

// If there is an error, show it
if ($error)
{
	// Create Notification
	$notification = $reportNtf->build(notification::ERROR, TRUE)->get();
	$content->append($notification);
	
	// Set Custom Message
	$reportMessage = $reportNtf->getMessage("error", "err.execute_error");
	$reportNtf->append($reportMessage);
	$reportNtf->appendCustomMessage("SQL Error: ".$dbc->getError());
}
else
{
	// Create result title
	$resultTitle = DOM::create("div", "", "", "resultTitle");
	DOM::append($resultTitle, $header);
	$content->append($resultTitle);
}

// Fetch result rows
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

// Append Test Content
$content->append($testContent);



// Return output
$poolClass = "testingPool_".$_GET['mid']."_".$_GET['qid'];
$poolClass = str_replace(".", "_", $poolClass);
return $content->getReport(".".$poolClass." .testResult");
//#section_end#
?>