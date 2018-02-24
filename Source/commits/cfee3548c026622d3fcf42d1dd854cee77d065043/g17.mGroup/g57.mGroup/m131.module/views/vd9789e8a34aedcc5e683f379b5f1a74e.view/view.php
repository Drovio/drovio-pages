<?php
//#section#[header]
// Module Declaration
$moduleID = 131;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\frames\windowFrame;

$pageStructureName = $_GET['pageStructure'];
$pageName = $_GET['name'];
$templateID = $_GET['templateId'];

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$globalContentWrapper = $HTMLContentBuilder->build()->get();

$textInfo = DOM::create('div');
DOM::append($globalContentWrapper, $textInfo);

// Structure Name
$labelName = DOM::create('span', 'Name : ');		
DOM::append($textInfo, $labelName);
$contentName = DOM::create('span', $pageName);		
DOM::append($textInfo, $contentName);

// Structure Name
$labelName = DOM::create('span', 'Structure: ');		
DOM::append($textInfo, $labelName);
$contentName = DOM::create('span', $pageStructureName);		
DOM::append($textInfo, $contentName);

$controls = DOM::create('div');
layoutContainer::floatPosition($controls, 'right');
DOM::append($globalContentWrapper, $controls);

$deleteStructureControl  = DOM::create('span', "Delete");
windowFrame::setAction($deleteStructureControl, $innerModules['templateObject'], 'deleteSequencePage', array('templateId' => $templateID));
DOM::append($controls, $deleteStructureControl);
 
// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>