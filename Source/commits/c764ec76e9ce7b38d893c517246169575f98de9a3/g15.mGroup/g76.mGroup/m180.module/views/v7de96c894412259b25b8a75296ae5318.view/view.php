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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \API\Content\analytics;
use \API\Content\pgVisitsMetrics;
use \UI\Presentation\tabControl; 
use \UI\Presentation\dataGridList;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;

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
$pData = pgVisitsMetrics::getData($date);

// Sort by time
//echo arsort($mData);
//echo arsort($pData); 



$mdUniqueIP = array();
$pgUniqueIP  = array();
$mdVisits = array();
$pgVisitsCount  = array();
$pgModuleToUrl = array();

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
$ratios[] = 0.05;
$ratios[] = 0.1;
$ratios[] = 0.1;
$ratios[] = 0.2;
$ratios[] = 0.1;
$ratios[] = 0.05;
$ratios[] = 0.15;
$ratios[] = 0.05;
$ratios[] = 0.1;

$ratios[] = 0.05;
$ratios[] = 0.05;

$mdlVisitsRawDtGrid->setColumnRatios($ratios);

$headers = array();
$headers[] = "time";
$headers[] = "browser";
$headers[] = "ip";
$headers[] = "uri";
$headers[] = "path";
$headers[] = "mdlID";
$headers[] = "auxiliary";
$headers[] = "guest";
$headers[] = "access";

$headers[] = "page";
$headers[] = "tGap";

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
	
	// Find Unique By IP
	if(array_key_exists($entry['ip'], $mdUniqueIP))
	{
		$mdUniqueIP[$entry['ip']]++;
	}
	else
	{
		$mdUniqueIP[$entry['ip']] = 1;
	}
	
	// Visits per Module
	if(array_key_exists($entry['moduleID'], $mdVisits))
	{
		$mdVisits[$entry['moduleID']]++;
	}
	else
	{
		$mdVisits[$entry['moduleID']] = 1;
	}
	
	//Auxilary Count
	if(!empty($entry['auxiliary']))
		$auxCount++;
}
// Plain Page Visits Data
$pgVisitsRawDtGrid = new dataGridList(); 
$pgVisitsRawDtGrid->build("", FALSE);

$headers = array();
$headers[] = "time";
$headers[] = "browser";
$headers[] = "ip";
$headers[] = "domain";
$headers[] = "uri";

$headers[] = "moduleID";
$headers[] = "static";
$headers[] = "dDesc";
$headers[] = "dPath";	

$pgVisitsRawDtGrid->setHeaders($headers);

foreach ($pData as $entry)
{
	// Pretify time
	$entry['time'] = strftime('%X' ,$entry['time']);
	
	// Pretify boolean variables
	$entry['static'] = intval($entry['static']) ? '['.$entry['static'].']TRUE' : '['.$entry['static'].']FALSE';
	
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
	$pgVisitsRawDtGrid->insertRow($entry);
	
	// Find Unique By IP
	if(array_key_exists($entry['ip'], $pgUniqueIP))
	{
		$pgUniqueIP[$entry['ip']]++;
	}
	else
	{
		$pgUniqueIP[$entry['ip']] = 1;
	}
	
	// Visits per Module
	if(array_key_exists($entry['moduleID'], $pgVisitsCount))
	{
		$pgVisitsCount[$entry['moduleID']]++;
	}
	else
	{
		$pgVisitsCount[$entry['moduleID']] = 1;
		$pgModuleToUrl[$entry['moduleID']] = $entry['uri'];
	}
}
//-------------------------------------------------------------------------

// Create TabControl
$tabControl = new tabControl();
$tabControl->build($id = "tbr_objectListTabber", FALSE);
$container = $HTMLContent->buildElement($tabControl->get())->get();

// structured
	$selected = TRUE;
	$id = "structuredData";	
	
	$tabContent = DOM::create('div');
	
	// Total Module Visits - Calls
	$dataRow = DOM::create('div', '', '', 'dataRow');
	DOM::append($tabContent, $dataRow);
	$header = DOM::create('span', ' Total Module Visits - Calls ', '', 'label');
	DOM::append ($dataRow, $header);
	$seperator = DOM::create('span', ':');
	DOM::append ($dataRow, $seperator);
	$count = count($mData);	
	$content = DOM::create('span', (string)$count, '', 'text');
	DOM::append ($dataRow, $content );
	$content = DOM::create('span', "( Modules : ".strval($count - $auxCount).", ", '', 'text');
	DOM::append($dataRow, $content);
	$content = DOM::create('span', "Auxiliaries : ".(string)$auxCount." )", '', 'text');
	DOM::append($dataRow, $content);
	
	// Page Visits
	$dataRow = DOM::create('div', '', '', 'dataRow');
	DOM::append($tabContent, $dataRow);
	$header = DOM::create('span', 'Page visitis ', '', 'label');
	DOM::append ($dataRow, $header);
	$seperator = DOM::create('span', ':');
	DOM::append ($dataRow, $seperator);
	$count = count($pData);
	$content = DOM::create('span', (string)$count, '', 'text');
	DOM::append ($dataRow, $content );
	
	
	// Total unique module vistis (by ip) $
	$dataRow = DOM::create('div', '', '', 'dataRow');
	DOM::append($tabContent, $dataRow);
	$header = DOM::create('span', 'Total unique module vistis (by ip) ', '', 'label');
	DOM::append ($dataRow, $header);
	$seperator = DOM::create('span', ':');
	DOM::append ($dataRow, $seperator);	
	$count = count($mdUniqueIP);
	$content = DOM::create('span', "".$count, '', 'text');
	DOM::append ($dataRow, $content );
	
	// Total unique page vistis (by ip) 
	$dataRow = DOM::create('div', '', '', 'dataRow');
	DOM::append($tabContent, $dataRow);
	$header = DOM::create('span', 'Total unique page vistis (by ip) ', '', 'label');
	DOM::append ($dataRow, $header);
	$seperator = DOM::create('span', ':');
	DOM::append ($dataRow, $seperator);
	$count = count($pgUniqueIP);
	$content = DOM::create('span', "".$count, '', 'text');
	DOM::append ($dataRow, $content );
	
	// Per page visitis 
	$dataSection = DOM::create('div', '', '', 'dataSection');
	DOM::append($tabContent, $dataSection);
	$header = DOM::create('span', 'Per page visitis ', '', 'header');
	DOM::append ($dataSection, $header);
	$content = DOM::create('div', '', '', 'content grid half');
	DOM::append($dataSection, $content);
	// pgVisitsCount
	$dtGridList = new dataGridList(); 
	$dtGridList->build("", FALSE);	
	$headers = array();
	$headers[] = "page ModuleID";
	$headers[] = "count";
	$dtGridList->setHeaders($headers);
	//$pgVisits = array_flip($pgVisitsCount);
	arsort($pgVisitsCount);
	foreach($pgVisitsCount as $key=>$value)
	{
		$row = array();
		$row[] = "".$key." => ".$pgModuleToUrl[$key];
		$row[] = "".$value; 
		$dtGridList->insertRow($row);
	}
	DOM::append($content, $dtGridList->get());
	
	// Per module Call - Usage 
	$dataSection = DOM::create('div', '', '', 'dataSection');
	DOM::append($tabContent, $dataSection);
	$header = DOM::create('span', 'Per Module visitis ', '', 'header');
	DOM::append ($dataSection, $header);
	$content = DOM::create('div', '', '', 'content grid half');
	DOM::append($dataSection, $content);
	// mdVisits
	$dtGridList = new dataGridList(); 
	$dtGridList->build("", FALSE);	
	$headers = array();
	$headers[] = "moduleID";
	$headers[] = "count";
	$dtGridList->setHeaders($headers);
	arsort($mdVisits);
	foreach($mdVisits as $key=>$value)
	{
		$row = array();
		$row[] = "".$value;
		$row[] = "".$key;
		$dtGridList->insertRow($row);
	}
	DOM::append($content, $dtGridList->get());
	
	
$header = DOM::create('span', 'Structured Data');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Plain Module Visits Data
	$selected = FALSE;
	$id = "plainMdData";
	
	$tabContent = $mdlVisitsRawDtGrid->get();
$header = DOM::create('span', 'Raw Data - Module Visits');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Plain Page Visits Data
	$selected = FALSE;
	$id = "plainPgData";	
	
	$tabContent = $pgVisitsRawDtGrid->get();
$header = DOM::create('span', 'Raw Data - Page Visits');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);


return $HTMLContent->getReport('#dataPresentation');
//#section_end#
?>