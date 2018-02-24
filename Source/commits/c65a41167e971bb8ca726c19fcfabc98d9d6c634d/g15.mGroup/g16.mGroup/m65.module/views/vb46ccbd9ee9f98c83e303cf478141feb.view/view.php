<?php
//#section#[header]
// Module Declaration
$moduleID = 65;

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

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Presentation");
importer::import("UI", "Presentation");
importer::import("UI", "Presentation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\units\modules\module;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \INU\Developer\redWIDE;


// Get module and view ids
$moduleInfoID = $_GET['mid'];
$itemID = $moduleInfoID."_info";

// Initialize module
$moduleObject = new module($moduleInfoID);
$moduleName = $moduleObject->getTitle();

// Initialize content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("module".$itemID, "moduleInfoEditor", TRUE);


// Get wide tabber
$header = $moduleName.":INFO";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>