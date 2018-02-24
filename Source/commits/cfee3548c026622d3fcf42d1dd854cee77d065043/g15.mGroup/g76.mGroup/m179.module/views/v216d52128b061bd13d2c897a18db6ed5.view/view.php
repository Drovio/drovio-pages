<?php
//#section#[header]
// Module Declaration
$moduleID = 179;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Content");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \API\Content\analytics;
use \UI\Presentation\dataGridList;


$data = analytics::getData();


// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();


// Create grid
$dtGridList = new dataGridList(); 
$dtGridList->build("", FALSE);
$HTMLContent->buildElement($dtGridList->get());

$headers = array();
$headers[] = "time";
$headers[] = "browser";
$headers[] = "address";
$headers[] = "port";
$headers[] = "uri";
$headers[] = "path";
$headers[] = "moduleID";
$headers[] = "auxilary";
$headers[] = "guest";
$headers[] = "access";

$dtGridList->setHeaders($headers);

foreach ($data as $entry)
{
	//$gridRow = array();
	print_r($entry);
	$dtGridList->insertRow($entry);
}

$container = $HTMLContent->get();
DOM::appendAttr($container, 'class', 'verticalScroll horrizontalScroll');

return $HTMLContent->getReport();
//#section_end#
?>