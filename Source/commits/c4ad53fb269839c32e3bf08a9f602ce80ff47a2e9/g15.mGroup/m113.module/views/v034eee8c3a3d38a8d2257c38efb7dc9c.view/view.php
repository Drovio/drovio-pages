<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\tabControl;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("Redback Publisher", "publisher", TRUE);
$mainContent = HTML::select(".uiMainContent")->item(0);

// Build the tabber
$tabber = new tabControl();
$pubTab = $tabber->build("publisherTabber")->get();
DOM::append($mainContent, $pubTab);

// Site Release Tab
$header = moduleLiteral::get($moduleID, "lbl_sitePublishTabHeader");
$tabPage = $page->getModuleContainer($moduleID, $action = "publishTabPage", $attr = array(), $startup = TRUE, $containerID = "internalPublisher");
$tabber->insertTab("siteRelease", $header, $tabPage, $selected = TRUE);

// Site Backup Tab
$header = moduleLiteral::get($moduleID, "lbl_siteBackupTabHeader");
$tabPage = $page->getModuleContainer($moduleID, $action = "backupTabPage", $attr = array(), $startup = TRUE, $containerID = "internalPublisher");
$tabber->insertTab("siteBack", $header, $tabPage, $selected = FALSE);

// Return output
return $page->getReport();
//#section_end#
?>