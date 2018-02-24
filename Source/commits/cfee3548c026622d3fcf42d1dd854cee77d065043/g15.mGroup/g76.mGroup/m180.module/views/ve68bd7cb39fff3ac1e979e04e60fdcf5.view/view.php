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
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;

// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->build('', 'analyticsPlainData');

$container = $HTMLContent->get();


$sForm = new simpleForm();
$sForm->build($moduleID, "analyticsViewer", $controls = FALSE);



// Form Buttons
$title = DOM::create("span", "Display");
$submit = $sForm->getSubmitButton($title, $id = "");
$sForm->append($submit);

$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
DOM::append($container, $filtersContainer);
DOM::append($filtersContainer, $sForm->get());

$filtersContainer = DOM::create('div', '', 'dataPresentation', 'dataPresentation');
$span= DOM::create('span', 'no Data');
DOM::append($filtersContainer, $span);
DOM::append($container, $filtersContainer);


return $HTMLContent->getReport();
//#section_end#
?>