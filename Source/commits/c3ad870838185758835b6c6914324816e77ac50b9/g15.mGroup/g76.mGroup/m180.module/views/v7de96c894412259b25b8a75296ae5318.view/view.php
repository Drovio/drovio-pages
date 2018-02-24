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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \API\Content\analytics;
use \API\Content\analytics\analysts\pageLoadsAnalyzer;
use \API\Content\pgVisitsMetrics;
use \UI\Forms\special\datepicker;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;

$sHasError = TRUE;
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$startDate = 'startDate_1';
	$sHasError = FALSE;
	$sDay = datepicker::getDay($_POST, $startDate);
	if (is_null($sDay) || empty($sDay))
	{
		$sHasError = TRUE;
	}
	$sMonth  = datepicker::getMonth($_POST, $startDate);
	if (is_null($sMonth) || empty($sMonth))
	{
		$sHasError = TRUE;
	}
	$sYear  = datepicker::getYear($_POST, $startDate);
	if (is_null($sYear) || empty($sYear))
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
	$date = $sYear."-".$sMonth."-".$sDay;
}

$mData = analytics::getData($date);
$pageLoadsAnalyzer = new pageLoadsAnalyzer();
$pData = $pageLoadsAnalyzer->getData($date, '', pageLoadsAnalyzer::FILTER_FULL);

$mdUniqueIP = array();
$pgUniqueIP  = array();
$mdVisits_main = array();
$mdVisits_aux = array();
$pgVisitsCount  = array();
$pgModuleToUrl = array();

// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$dbc = new interDbConnection();

//-------------------------------------------------------------------------
// Calculate Data

$auxCount = 0;
$mdlIdMap = array();
$dbq = new dbQuery("361601426", "units.modules");
// Module Hits
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
	
	// Find Unique By IP
	if(array_key_exists($entry['ip'], $mdUniqueIP))
	{
		$mdUniqueIP[$entry['ip']]++;
	}
	else
	{
		$mdUniqueIP[$entry['ip']] = 1;
	}
	
	if(empty($entry['auxiliary']))
	{
		// Visits per Module
		if(array_key_exists($entry['moduleID'], $mdVisits_main))
		{
			$mdVisits_main[$entry['moduleID']]++;
		}
		else
		{
			$mdVisits_main[$entry['moduleID']] = 1;
		}
	}
	else
	{
		//Auxilary Count
		$auxCount++;
		
		if(!array_key_exists($entry['moduleID'], $mdVisits_main))
		{
			$mdVisits_main[$entry['moduleID']] = 0;
		}
		if(array_key_exists($entry['moduleID'], $mdVisits_aux))
		{
			$mdVisits_aux[$entry['moduleID']]++;
		}
		else
		{
			$mdVisits_aux[$entry['moduleID']] = 1;
		}
	}
}

// Page Visits Data
foreach ($pData as $entry)
{
	// Pretify time
	$entry['time'] = strftime('%X' ,$entry['time']);
	
	// Pretify boolean variables
	$entry['static'] = intval($entry['static']) ? '['.$entry['static'].']TRUE' : '['.$entry['static'].']FALSE';
	
/*
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
	*/
	
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
	
	// Find page visits via reference links
}
//-------------------------------------------------------------------------

// Create TabControl
$container = $HTMLContent->build()->get();
	
// Total Module Visits - Calls
$dataRow = DOM::create('div', '', '', 'dataRow');
DOM::append($container, $dataRow);
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
DOM::append($container, $dataRow);
$header = DOM::create('span', 'Page Visits ', '', 'label');
DOM::append ($dataRow, $header);
$seperator = DOM::create('span', ':');
DOM::append ($dataRow, $seperator);
$count = count($pData);
$content = DOM::create('span', (string)$count, '', 'text');
DOM::append ($dataRow, $content );	

// Total unique module vistis (by ip) $
$dataRow = DOM::create('div', '', '', 'dataRow');
DOM::append($container, $dataRow);
$header = DOM::create('span', 'Total unique module visits (by ip) ', '', 'label');
DOM::append ($dataRow, $header);
$seperator = DOM::create('span', ':');
DOM::append ($dataRow, $seperator);	
$count = count($mdUniqueIP);
$content = DOM::create('span', "".$count, '', 'text');
DOM::append ($dataRow, $content );

// Total unique page vistis (by ip) 
$dataRow = DOM::create('div', '', '', 'dataRow');
DOM::append($container, $dataRow);
$header = DOM::create('span', 'Total unique page visits (by ip) ', '', 'label');
DOM::append ($dataRow, $header);
$seperator = DOM::create('span', ':');
DOM::append ($dataRow, $seperator);
$count = count($pgUniqueIP);
$content = DOM::create('span', "".$count, '', 'text');
DOM::append ($dataRow, $content );

// Per page visitis 
$dataSection = DOM::create('div', '', '', 'dataSection');
DOM::append($container, $dataSection);
$header = DOM::create('span', 'Per page visitis ', '', 'header');
DOM::append ($dataSection, $header);
$content = DOM::create('div', '', '', 'content grid half');
DOM::append($dataSection, $content);
// pgVisitsCount
$dtGridList = new dataGridList(); 
$dtGridList->build("", FALSE);	
$headers = array();
$headers[] = "Page Url";
$headers[] = "count";
$dtGridList->setHeaders($headers);
arsort($pgVisitsCount);
foreach($pgVisitsCount as $key=>$value)
{
	$row = array();
	$row[] = $pgModuleToUrl[$key];
	$row[] = "".$value; 
	$dtGridList->insertRow($row);
}
DOM::append($content, $dtGridList->get());

// Per module Call - Usage 
$dataSection = DOM::create('div', '', '', 'dataSection');
DOM::append($container, $dataSection);
$header = DOM::create('span', 'Per Module visits ', '', 'header');
DOM::append ($dataSection, $header);
$content = DOM::create('div', '', '', 'content grid half');
DOM::append($dataSection, $content);
// mdVisits
$dtGridList = new dataGridList(); 
$dtGridList->build("", FALSE);	
$headers = array();
$headers[] = "moduleID";
$headers[] = "Main View Hits";
$headers[] = "Other Views Hits";
$dtGridList->setHeaders($headers);
arsort($mdVisits_main);
foreach($mdVisits_main as $key=>$value)
{
	$row = array();
	$row[] = "".$key;
	$row[] = "".$value;
	$row[] = "".$mdVisits_aux[$key];
	$dtGridList->insertRow($row);
}
DOM::append($content, $dtGridList->get());


return $HTMLContent->getReport($_POST['holder']);
//#section_end#
?>