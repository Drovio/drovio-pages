<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Presentation\tabControl;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "publisher", TRUE);
$mainContent = HTML::select(".publisher .uiMainContent")->item(0);

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