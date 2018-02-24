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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\datepicker;
use \UI\Html\HTMLContent;
use \UI\Presentation\tabControl; 



// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();

// Create TabControl
$tabControl = new tabControl();
$tabControl->build($id = "", FALSE);
$container = $HTMLContent->buildElement($tabControl->get())->get();

// structured
$selected = TRUE;
$id = "basicView";
	$tabContent = DOM::create('div');

	$sForm = new simpleForm();
	$sForm->build($moduleID, "structuredData", $controls = FALSE);	
	
	$rangeSelector = DOM::create('div', '', '', 'rangeSelector');
	$sForm->append($rangeSelector);
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = DOM::create("span", "From");//moduleLiteral::get($moduleID, "lbl_templateType");
	DOM::append($datepickerWrapper, $label);
	$datepicker->build('startDate_1');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = DOM::create("span", "To");//moduleLiteral::get($moduleID, "lbl_templateType");
	DOM::append($datepickerWrapper, $label);
	$datepicker->build('endDate_1');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	// Form Buttons
	$title = DOM::create("span", "Display");
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($tabContent, $filtersContainer);
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', '', 'dataPresentation');
	//$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "structuredData", $attr = array(), $startup = TRUE, 'basicViewData');
	DOM::append($dataPresentation, $content );
	DOM::append($tabContent, $dataPresentation);

$header = DOM::create('span', 'Basic View');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// raw
$selected = FALSE;
$id = "advancedView";
	$tabContent = DOM::create('div');

	$sForm = new simpleForm();
	$sForm->build($moduleID, "analyticsViewer", $controls = FALSE);
	
	
	$datesSelector = DOM::create('div');
	$sForm->append($datesSelector);
	
	$fromWrapper = DOM::create('div');
	DOM::append($datesSelector, $fromWrapper);
	$datepicker = new datepicker();
	$label = DOM::create("span", "From");//moduleLiteral::get($moduleID, "lbl_templateType");
	DOM::append($fromWrapper, $label);
	$datepicker->build('startDate_2');
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
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($tabContent, $filtersContainer);
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', 'dataPresentation', 'dataPresentation');
	$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "hitsBasicView", $attr = array(), $startup = TRUE, 'rvSystemReach');
	DOM::append($dataPresentation, $content );
	DOM::append($tabContent, $dataPresentation);

$header = DOM::create('span', 'Advanced View');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

return $HTMLContent->getReport();
//#section_end#
?>