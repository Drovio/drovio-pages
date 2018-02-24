<?php
//#section#[header]
// Module Declaration
$moduleID = 230;

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
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;

$mcontent = new MContent($moduleID);
$mcontent->build("","siteStatusEditor",TRUE);
$statusContent = $mcontent->get();

//$header = HTML::create("h3","Change your site's status here :","","changeStatusHeader");
//$mcontent->append($header );

// Create Module Page
$popupContent = new popup();
$popupContent->position("bottom","right");
$popupContent->build($statusContent); 


// Return output
return $popupContent->getReport();
//#section_end#
?>