<?php
//#section#[header]
// Module Declaration
$moduleID = 180;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Content");
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Content\analytics\analysts\pageLoadsAnalyzer;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \UI\Presentation\tabControl;

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

$pageLoadsAnalyzer = new pageLoadsAnalyzer();
//$pData = $pageLoadsAnalyzer->getData($date, '');
$pData = $pageLoadsAnalyzer->getData($date, '', pageLoadsAnalyzer::FILTER_FULL);

// Create Module Content
$HTMLContent = new MContent();
$actionFactory = $HTMLContent->getActionFactory();
$dbc = new dbConnection();

//-------------------------------------------------------------------------
// Calculate Data
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

$mdlIdMap = array();
$dbq = new dbQuery("361601426", "units.modules");
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
}
//-------------------------------------------------------------------------
// Plain Page Visits Data
$HTMLContent->buildElement($pgVisitsRawDtGrid->get())->get();


return $HTMLContent->getReport($_POST['holder']);
//#section_end#
?>