<?php
//#section#[header]
// Module Declaration
$moduleID = 129;

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
importer::import("API", "Developer");
importer::import("UI", "Html");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
// Usage
use \API\Developer\ebuilder\template;
use \UI\Html\HTMLContent;
use \INU\Views\fileExplorer;

$templateID = $_GET['id'];

$templateManager = new template();
$templateManager->load($templateID);

$assetsPath = $templateManager->getAssetsPath(FALSE);
$fileExplorer = new fileExplorer($assetsPath, 'assetsExplorer');


// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$ModuleHTMLContent = $HTMLContentBuilder->buildElement($fileExplorer->build()->get())->get();


// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>