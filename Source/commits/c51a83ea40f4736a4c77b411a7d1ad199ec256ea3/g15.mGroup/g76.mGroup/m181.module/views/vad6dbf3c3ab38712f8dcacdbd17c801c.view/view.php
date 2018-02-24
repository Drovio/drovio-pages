<?php
//#section#[header]
// Module Declaration
$moduleID = 181;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \API\Model\units\sql\dbQuery;
use \SYS\Comm\db\dbConnection;

// Create Module Page
$HTMLContent = new MContent();
$actionFactory = $HTMLContent->getActionFactory();
$dbc = new dbConnection();

$container = $HTMLContent->build()->get();

// totalPersonCount
$dataRow = DOM::create('div', '', '', 'dataRow');
DOM::append($container, $dataRow);
$header = DOM::create('span', 'Persons Count', '', 'label');
DOM::append ($dataRow, $header);
$seperator = DOM::create('span', ':');
DOM::append ($dataRow, $seperator);

$dbq = new dbQuery("948500592", "profile.person");
$defaultResult = $dbc->execute($dbq);
$count = 0;
while ($row = $dbc->fetch($defaultResult)) 
{
	$count = $row['COUNT(RB_person.id)'];
}
$content = DOM::create('span', $count, '', 'text');
DOM::append($dataRow, $content);

// Total Account Count 
$dataRow = DOM::create('div', '', '', 'dataRow');
DOM::append($container, $dataRow);
$header = DOM::create('span', 'Accounts Count', '', 'label');
DOM::append ($dataRow, $header);
$seperator = DOM::create('span', ':');
DOM::append ($dataRow, $seperator);

$dbq = new dbQuery("1957153880", "profile.account");
$defaultResult = $dbc->execute($dbq);
$accCount = 0;
while ($row = $dbc->fetch($defaultResult)) 
{
	$accCount = $row['COUNT(PLM_account.id)'];
}
// Get managed account count
$dbq = new dbQuery("1962970958", "profile.account");
$defaultResult = $dbc->execute($dbq);
$mAccCount = 0; 
while ($row = $dbc->fetch($defaultResult)) 
{
	$mAccCount++;
}
$content = DOM::create('span', $accCount, '', 'text');
DOM::append($dataRow, $content);
$content = DOM::create('span', "( Admin Accounts : ".strval(intval($accCount) - $mAccCount).", ", '', 'text');
DOM::append($dataRow, $content);
$content = DOM::create('span', "Managed Accounts : ".(string)$mAccCount." )", '', 'text');
DOM::append($dataRow, $content);

// usersInGroup 
$dataSection = DOM::create('div', '', '', 'dataSection');
DOM::append($container, $dataSection);
$header = DOM::create('span', 'Users By Groups', '', 'header');
DOM::append($dataSection, $header);
$content = DOM::create('div', '', '', 'content grid half');
DOM::append($dataSection, $content);
	
$dbq = new dbQuery("2111264238", "profile.account");
$defaultResult = $dbc->execute($dbq);			

// Create grid
$dtGridList = new dataGridList(); 
$dtGridList->build("", FALSE);

$headers = array();
$headers[] = "Group Name";
$headers[] = "Count";
$dtGridList->setHeaders($headers);

while ($row = $dbc->fetch($defaultResult)) 
{
	$gridrow = array();
	
	$gridrow[] = $row['name'];
	$gridrow[] = $row['COUNT(PLM_accountAtGroup.account_id)'];
	
	$dtGridList->insertRow($gridrow);
}
DOM::append($content, $dtGridList->get());

// Return output
return $HTMLContent->getReport();
//#section_end#
?>