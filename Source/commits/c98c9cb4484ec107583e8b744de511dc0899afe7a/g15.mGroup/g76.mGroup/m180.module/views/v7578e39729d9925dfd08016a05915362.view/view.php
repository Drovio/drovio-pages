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
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\datepicker;
use \UI\Html\HTMLContent;



// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->build('', 'analyticsPlainData');

$container = $HTMLContent->get();

$sForm = new simpleForm();
$sForm->build($moduleID, "analyticsViewer", $controls = FALSE);

$datesSelector = DOM::create('div');
$sForm->append($datesSelector);

$fromWrapper = DOM::create('div');
DOM::append($datesSelector, $fromWrapper);
$datepicker = new datepicker();
$label = DOM::create("span", "From");//moduleLiteral::get($moduleID, "lbl_templateType");
DOM::append($fromWrapper, $label);
$datepicker->build($id = 'startDate');
DOM::append($fromWrapper, $datepicker->get());

// day
$resource = array();
for($i = 1; $i <= 31; $i++)
{
	//Selector values
	$resource[(string)$i] = (string)$i;
}	
$title =  DOM::create("span", "Day");//moduleLiteral::get($moduleID, "lbl_templateType");
$input = $sForm->getResourceSelect($name = "sDay", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Month
$resource = array();
for($i = 1; $i <= 12; $i++)
{
	//Selector values
	$resource[(string)$i] = (string)$i;
}	
$title =  DOM::create("span", "Month");//moduleLiteral::get($moduleID, "lbl_templateType");
$input = $sForm->getResourceSelect($name = "sMonth", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Year
$title = DOM::create("span", "Year");//moduleLiteral::get($moduleID, "lbl_templateName"); 
$input = $sForm->getInput($type = "text", $name = "sYear", $value = "", $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Form Buttons
$title = DOM::create("span", "Display");
$submit = $sForm->getSubmitButton($title, $id = "");
$sForm->append($submit);

$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
DOM::append($container, $filtersContainer);
DOM::append($filtersContainer, $sForm->get());

$dataPresentation = DOM::create('div', '', 'dataPresentation', 'dataPresentation');
//$content = DOM::create('span', 'no Data');
$content = $HTMLContent->getModuleContainer($moduleID, "analyticsViewer", $attr = array(), $startup = TRUE, 'rvSystemReach');
DOM::append($dataPresentation, $content );
DOM::append($container, $dataPresentation);



return $HTMLContent->getReport();
//#section_end#
?>