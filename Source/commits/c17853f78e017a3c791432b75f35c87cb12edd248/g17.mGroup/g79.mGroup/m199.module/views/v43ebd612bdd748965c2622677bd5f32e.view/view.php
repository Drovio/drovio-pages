<?php
//#section#[header]
// Module Declaration
$moduleID = 199;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;

// Create Module Page
$HTMLModulePage = new HTMLModulePage();
$actionFactory = $HTMLModulePage->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$HTMLModulePage->build($pageTitle);

$stepsBar = DOM::create('div', '', '', 'stepsBar');
$HTMLModulePage->appendToSection('mainContent', $stepsBar);
$index = 0;
// Step 1 -
$step = DOM::create('div', '', '', 'step current');
	$index++;	
	$num = DOM::create('span', "".$index.". ");
	$indicator = DOM::create('div', '', '', 'indicator');
	DOM::append($indicator, $num);
	DOM::append($step, $indicator);
	$label = DOM::create('div', moduleLiteral::get($moduleID, 'lbl_create'), '', 'label');
	DOM::append($step, $label);
DOM::append($stepsBar, $step);
// Step 2 -
$step = DOM::create('div', '', '', 'step disabled');
	$index++;	
	$num = DOM::create('span', "".$index.". ");
	$indicator = DOM::create('div', '', '', 'indicator');
	DOM::append($indicator, $num);
	DOM::append($step, $indicator);
	$label = DOM::create('div', moduleLiteral::get($moduleID, 'lbl_template'), '', 'label');
	DOM::append($step, $label);
DOM::append($stepsBar, $step);
// Step 3 -
$step = DOM::create('div', '', '', 'step disabled');
	$index++;	
	$num = DOM::create('span', "".$index.". ");
	$indicator = DOM::create('div', '', '', 'indicator');
	DOM::append($indicator, $num);
	DOM::append($step, $indicator);
	$label = DOM::create('div', moduleLiteral::get($moduleID, 'lbl_extension'), '', 'label');
	DOM::append($step, $label);
DOM::append($stepsBar, $step);
// Step 4 -
$step = DOM::create('div', '', '', 'step disabled');
	$index++;	
	$num = DOM::create('span', "".$index.". ");
	$indicator = DOM::create('div', '', '', 'indicator');
	DOM::append($indicator, $num);
	DOM::append($step, $indicator);
	$label = DOM::create('div', moduleLiteral::get($moduleID, 'lbl_server'), '', 'label');
	DOM::append($step, $label);
DOM::append($stepsBar, $step);

$contentHolder = DOM::create();
$HTMLModulePage->appendToSection('mainContent', $contentHolder);

// Return output
return $HTMLModulePage->getReport();
//#section_end#
?>