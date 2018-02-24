<?php
//#section#[header]
// Module Declaration
$moduleID = 180;

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
importer::import("API", "Content");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Content\analytics;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\dataGridList;


$sHasError = TRUE;
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$sHasError = FALSE;
	if (is_null($_POST['sDay']) || empty($_POST['sDay']))
	{
		$sHasError = TRUE;
	}
	if (is_null($_POST['sMonth']) || empty($_POST['sMonth']))
	{
		$sHasError = TRUE;
	}
	if (is_null($_POST['sYear']) || empty($_POST['sYear']))
	{
		$sHasError = TRUE;
	}
}

if($sHasError)
{
	$date = '';
}
else
{
	$date = $_POST['sYear']."-".$_POST['sMonth']."-".$_POST['sDay'];
}

$mData = analytics::getData($date);

$mdUniqueIP = array();
$mdVisits = array();

// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$dbc = new interDbConnection();

//-------------------------------------------------------------------------
// Calculate Data
// Plain Module Visits Data
// Create grid
$mdlVisitsRawDtGrid = new dataGridList(); 
$mdlVisitsRawDtGrid->build("", FALSE);

$ratios = array();
$ratios['time'] = 0.05;
$ratios['browser'] = 0.1;
$ratios['ip'] = 0.1;
$ratios['uri'] = 0.2;
$ratios['path'] = 0.1;
$ratios['moduleID'] = 0.05;
$ratios['auxiliary'] = 0.15;
$ratios['guest'] = 0.05;
$ratios['access'] = 0.1;

$ratios['page'] = 0.05;
$ratios['tGap'] = 0.05;

$mdlVisitsRawDtGrid->setColumnRatios($ratios);

$headers = array();
$headers['time'] = "time";
$headers['browser'] = "browser";
$headers['ip'] = "ip";
$headers['uri'] = "uri";
$headers['path'] = "path";
$headers['moduleID'] = "mdlID";
$headers['auxiliary'] = "auxiliary";
$headers['guest'] = "guest";
$headers['access'] = "access";

$headers['page'] = "page";
$headers['tGap'] = "tGap";

$mdlVisitsRawDtGrid->setHeaders($headers);

$auxCount = 0;
$mdlIdMap = array();
$dbq = new dbQuery("361601426", "units.modules");
foreach ($mData as $entry)
{
	// Pretify time
	$entry['time'] = strftime('%X' ,$entry['time']);
	
	// Pretify boolean variables
	$entry['guest'] = intval($entry['guest']) ? '['.$entry['guest'].']TRUE' : '['.$entry['guest'].']FALSE';
	
	// Add Module sort desc in addition to code
	if(!array_key_exists($entry['moduleID'], $mdlIdMap))
	{
		$attr = array();
		$attr['id'] = $entry['moduleID'];
		$defaultResult = $dbc->execute($dbq, $attr);
		while ($row = $dbc->fetch($defaultResult)) 
		{
			 $mdlIdMap[$entry['moduleID']] = $row['module_title'];
		}	
	}
	$title = $mdlIdMap[$entry['moduleID']];
	$entry['moduleID'] = "[".$entry['moduleID']."] ".$title;
	
	$mdlVisitsRawDtGrid->insertRow($entry);
}

//-------------------------------------------------------------------------
// Plain Module Visits Data
$container = $HTMLContent->build()->get();

DOM::append($container, $mdlVisitsRawDtGrid->get());


return $HTMLContent->getReport($_POST['holder']);
//#section_end#
?>