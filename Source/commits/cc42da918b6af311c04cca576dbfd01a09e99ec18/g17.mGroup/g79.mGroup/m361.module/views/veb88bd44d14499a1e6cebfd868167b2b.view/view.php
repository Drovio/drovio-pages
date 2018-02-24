<?php
//#section#[header]
// Module Declaration
$moduleID = 361;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("ESS", "Environment");
importer::import("UI", "Content");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \UI\Content\HTMLFrame;

// Create module content
$pageContent = new MContent();
$pageContent->build("", "websitePreviewFrameContainer");

// Get website id and name
$websiteID = engine::getVar('id');

// Get preview url
$params = array();
$params['id'] = $websiteID;
$params['page'] = engine::getVar('page');
$src = url::resolve("web", "/websites/preview.php", $params);

// Create iframe
$iframe = new HTMLFrame();
$previewFrame = $iframe->build($src, $name = "websitePreview", $id = "websitePreview", $class = "", $sandbox = array())->get();
$pageContent->append($previewFrame);

// Return report
return $pageContent->getReport(".websitePreviewContainer .testingContainer");
//#section_end#
?>