<?php
//#section#[header]
// Module Declaration
$moduleID = 156;

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
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Resources\filesystem\fileManager;
use \API\Services\bmapp\project;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;
use \INU\Forms\HTMLEditor;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");

// Build the module
$page->build($_GET['fn'], "documentPreview");

// Get Full File Path
$project = new project(1);
$projectFolder = $project->getFolder();
$documentFilePath = systemRoot.urldecode($projectFolder."/".$_GET['sp']."/".$_GET['fn']);
$documentContents = $project->getContents($documentFilePath);

// Create Document Form
$dForm = new simpleForm();
$documentFormElement = $dForm->build($moduleID)->get();
$page->appendToSection("mainContent", $documentFormElement);

// Create Document Editor Container
$documentEditorContainer = DOM::create("div", "", "", "documentEditor");
$dForm->append($documentEditorContainer);

// Create Document Editor
$editor = new HTMLEditor();
$documentEditor = $editor->build($documentContents, "document")->get();
DOM::append($documentEditorContainer, $documentEditor);


// Return output
return $page->getReport();
//#section_end#
?>