<?php
//#section#[header]
// Module Declaration
$moduleID = 157;

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
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("INU", "Forms");
//#section_end#
//#section#[code]
use \API\Services\bmapp\project;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;
use \INU\Forms\HTMLEditor;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");

// Build the module
$page->build("Bug Report", "bugReportTemp");

// Get Full File Path
$project = new project(1);
$projectFolder = $project->getFolder();
$bugsFilePath = systemRoot.urldecode($projectFolder."/Bugs/bugs.html");

// Get Contents
$bugContents = $project->getContents($bugsFilePath);

// Create editor
$editor = new HTMLEditor();
$bugEditor = $editor->build($bugContents)->get();

$page->appendToSection("mainContent", $bugEditor);



//


//$bugViewer = DOM::create("div");
//print_r($bugContents);
//DOM::innerHTML($bugViewer, $bugContents);
//$page->appendToSection("mainContent", $bugViewer);


// Return output
return $page->getReport();
//#section_end#
?>