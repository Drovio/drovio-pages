<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;
$innerModules['themesManager'] = 128;

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

$templateID = $_GET['templateId'];
$objectName = $_GET['objectName'];


// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->build('', 'itemRow');

$itemRow = $HTMLContent->get();
DOM::data($itemRow, "effects", array('glow'));

//$textInfo = DOM::create('div', '', '', 'itemRow');
//DOM::append($globalContentWrapper, $textInfo);

// Structure Name
$content = DOM::create('div', '', '', 'content');
DOM::append($itemRow, $content);
$labelName = DOM::create('span', 'Name : ');		
DOM::append($content, $labelName);
$contentName = DOM::create('span', $objectName);		
DOM::append($content, $contentName);

DOM::attr($content, "data-pageSelector", "display");

$attr = array();
$attr['templateId'] = $templateID;
$attr['holder'] = '#themeStructureSelector';
$attr['theme'] = $objectName;
$actionFactory->setModuleAction($content, $moduleID, "themeStructureSelector", "", $attr);

$controls = DOM::create('div', '', '', 'controls');
layoutContainer::floatPosition($controls, 'right');
DOM::append($itemRow, $controls);

$deleteStructureControl  = DOM::create('span', "Delete");
windowFrame::setAction($deleteStructureControl, $innerModules['templateObject'], 'deleteTheme', array('templateId' => $templateID, 'objectName' => $objectName));
DOM::append($controls, $deleteStructureControl);


// Return output
return $HTMLContent->getReport();
//#section_end#
?>