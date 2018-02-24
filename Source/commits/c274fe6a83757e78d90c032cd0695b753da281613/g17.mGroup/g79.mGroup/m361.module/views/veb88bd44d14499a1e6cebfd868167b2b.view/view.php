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
importer::import("API", "Security");
importer::import("DEV", "Websites");
importer::import("ESS", "Environment");
importer::import("UI", "Content");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Content\HTMLFrame;
use \DEV\Websites\website;

// Create module content
$pageContent = new MContent($moduleID);
$pageContent->build("", "websitePreviewFrameContainer", TRUE);

// Get website id
$websiteID = engine::getVar('id');
$website = new website($websiteID);
$websiteInfo = $website->info();
$websiteIdentifier = (empty($websiteInfo['name']) ? $websiteID : $websiteInfo['name']);

// Get developer key as token
$projectKeys = accountKey::getProjectKeys($websiteID);
$token = $projectKeys[0]['akey'];

// Get preview url
$displayPage = engine::getVar('page');
$src = url::resolve("web", $websiteIdentifier."/preview/".$token."/".$displayPage);

// Create iframe
$iframe = new HTMLFrame();
$previewFrame = $iframe->build($src, $name = "websitePreview", $id = "websitePreview", $class = "", $sandbox = array())->get();
$frameContainer = HTML::select(".websitePreviewFrameContainer .websitePreviewFrame")->item(0);
DOM::append($frameContainer, $previewFrame);


// Set footer
$footer = HTML::select(".websitePreviewFrameContainer .footer")->item(0);
$title = $pageContent->getLiteral($name = "lbl_websiteFullPage");
$fullScreenLink = $pageContent->getWeblink($src, $title, $target = "_blank", $moduleID = NULL, $viewName = "", $attr = array(), $class = "link");
DOM::append($footer, $fullScreenLink);

// Return report
return $pageContent->getReport(".websitePreviewContainer .testingContainer");
//#section_end#
?>