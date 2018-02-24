<?php
//#section#[header]
// Module Declaration
$moduleID = 179;

// Inner Module Codes
$innerModules = array();
$innerModules['rvAnalytics'] = 180;
$innerModules['rvSystemReach'] = 181;
$innerModules['rvTimeTraffic'] = 182;

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
use \UI\Html\HTMLModulePage;
use \UI\Presentation\tabControl;


$pageTitle = 'Reporting';//moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$HTMLModulePage = new HTMLModulePage("OneColumnFullscreen");
$HTMLModulePage->build($pageTitle);
$actionFactory = $HTMLModulePage->getActionFactory();

$tabControl = new tabControl();
$tabControl->build($id = "", FALSE);
$HTMLModulePage->appendToSection('mainContent', $tabControl->get());

// Plain Module Visits Data
	$selected = TRUE;
	$id = "rvAnalytics";
	
	$tabContent = $HTMLModulePage->getModuleContainer($innerModules['rvAnalytics'], "", $attr = array(), $startup = TRUE, 'rvAnalytics');
	
	
$header = DOM::create('span', 'Redback Reach');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Plain Module Visits Data
	$selected = FALSE;
	$id = "rvSystemReach";
	
	$tabContent = $HTMLModulePage->getModuleContainer($innerModules['rvSystemReach'], "", $attr = array(), $startup = TRUE, 'rvSystemReach');
$header= DOM::create('span', 'Redback User Stats');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Plain Module Visits Data
	$selected = FALSE;
	$id = "rvTimeTraffic";

	$tabContent = $HTMLModulePage->getModuleContainer($innerModules['rvTimeTraffic'], "", $attr = array(), $startup = TRUE, 'rvTimeTraffic');
$header = DOM::create('span', 'Time Traffic');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);


// Return the report
return $HTMLModulePage->getReport(HTMLModulePage::getPageHolder());
//#section_end#
?>