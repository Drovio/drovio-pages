<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;

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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\layoutContainer;

$templateID = $_GET['templateId'];
$objectName = $_GET['objectName'];
$holder = '#'.$_GET['hld'].' .deletePromtHolder ';


// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->build('', 'itemRow');

$itemRow = $HTMLContent->get();
DOM::data($itemRow, "effects", array('glow'));

$viewer = DOM::create('div', '', '', 'itemInfo rowContent');
DOM::append($itemRow, $viewer);

$deletePromtHolder = DOM::create('div', '', '', 'deletePromtHolder rowContent noDisplay');
DOM::append($itemRow, $deletePromtHolder);

// Structure Name
$content = DOM::create('div', '', '', 'content');
DOM::append($viewer, $content);
$textWrapper = DOM::create('div', '', '', '');
	$contentName = DOM::create('span', $objectName);		
	DOM::append($textWrapper, $contentName);
DOM::append($content, $textWrapper);

DOM::attr($content, "data-pageSelector", "close");

// On click
$attr = array('templateId' => $templateID, 'objectName' => $objectName);
$actionFactory->setModuleAction($content, $moduleID, "psObjectEditor", "", $attr);


// Controls
$controls = DOM::create('div', '', '', 'controls');
layoutContainer::floatPosition($controls, 'right');
DOM::append($viewer, $controls);

// Delete Control
$control = DOM::create('div', '', '', 'control');
DOM::append($controls, $control);
$deleteStructureControl  = moduleLiteral::get($moduleID, "lbl_delete", array(), TRUE);
$actionFactory->setModuleAction($deleteStructureControl, $innerModules['templateObject'], 'deletePageStructure', '', array('templateId' => $templateID, 'objectName' => $objectName, 'holder' => $holder));
DOM::append($control, $deleteStructureControl);


// Return output
return $HTMLContent->getReport();
//#section_end#
?>