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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\tabControl;

$templateID = $_GET['id'];

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$globalContainer = $HTMLContentBuilder->build()->get();




// Template Graphical View
$tabControl = new tabControl();
$tabWrapper = $tabControl->get_control("tbr_templateGraphicalView", FALSE);
//DOM::attr($globalContainer, "style", "height:100%;");
DOM::append($globalContainer, $tabWrapper);

// Thumbs View
$selected = TRUE;
$id = "thumbsView";
$tabContent = DOM::create('div');
layoutContainer::border($tabContent , "", "s");
DOM::attr($tabContent, "style", "height:500px;");

$header = DOM::create('span', 'Thumbs View');//moduleLiteral::get($moduleID, "hdr_allTemplates");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// HTML Live View
$selected = FALSE;
$id = "htmlLiveView";
$tabContent = DOM::create('div');
layoutContainer::border($tabContent , "", "s");
DOM::attr($tabContent, "style", "height:500px;");

$header = DOM::create('span', 'HTML Live View');//moduleLiteral::get($moduleID, "hdr_allTemplates");
$tabControl->insertTab($id, $header, $tabContent, $selected);





// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>