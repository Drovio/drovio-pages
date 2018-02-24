<?php
//#section#[header]
// Module Declaration
$moduleID = 155;

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
importer::import("API", "Services");
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Services\bmapp\project;
use \UI\Html\HTMLModulePage;
use \INU\Views\fileExplorer;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");

// Build the module
$page->build("Project Home", "projectPage");


$project = new project(1);
$projectFolder = $project->getFolder();

$explorer = new fileExplorer($projectFolder, "bmapp_project", "Redback");
$container = $explorer->build()->get();
$page->appendToSection("mainContent", $container);

// Return output
return $page->getReport();
//#section_end#
?>