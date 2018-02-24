<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();
// Build page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "appCenterPage", TRUE);


$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$header = HTML::select(".logoContainer .title")->item(0);
DOM::append($header, $title);



// Categories
$title = moduleLiteral::get($moduleID, "lbl_categoriesHeader");
$header = HTML::select(".categoryContainer .header")->item(0);
DOM::append($header, $title);


$title = moduleLiteral::get($moduleID, "lbl_allApps");
$header = HTML::select(".listItem.all")->item(0);
DOM::append($header, $title);
$actionFactory->setModuleAction($header, $moduleID, "appList");


// Help Links
$title = moduleLiteral::get($moduleID, "lbl_helpTitle");
$header = HTML::select(".helpLinks .header")->item(0);
DOM::append($header, $title);


$title = moduleLiteral::get($moduleID, "lbl_help_start");
$url = url::resolve("developer", "/docs/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".helpLinks .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_help_support");
$url = url::resolve("developer", "/support/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".helpLinks .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_help_terms");
$url = url::resolve("www", "/help/legal/terms/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".helpLinks .listItem")->item(2);
DOM::append($header, $wl);


// Application container
$title = moduleLiteral::get($moduleID, "lbl_apps");
$header = HTML::select(".appContainer .header")->item(0);
DOM::append($header, $title);


// Create module container
$appsContainer = HTML::select(".appContainer .apps")->item(0);
$appsListContainer = $page->getModuleContainer($moduleID, $action = "appList", $attr, $startup = TRUE, $containerID = "appsListContainer");
DOM::append($appsContainer, $appsListContainer);


// Video container
$appVideo = HTML::select(".videoContainer .appCenterVideo")->item(0);


// Application container
$title = moduleLiteral::get($moduleID, "lbl_featuring");
$header = HTML::select(".featured .header")->item(0);
DOM::append($header, $title);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/m/videos/appCenter/promo.webm");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/webm");
DOM::append($appVideo, $vSource);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/m/videos/appCenter/promo.mp4");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/mp4");
DOM::append($appVideo, $vSource);

// No support
$title = moduleLiteral::get($moduleID, "lbl_videoNotSupported");
$header = HTML::select("video p.pNoSupport")->item(0);
DOM::append($header, $title);

return $page->getReport();
//#section_end#
?>