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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLContent;
use \UI\Presentation\popups\popup;

// Build content
$pageContent = new HTMLContent();
$pageContent->build("", "promo", TRUE);


// Promo video
$title = moduleLiteral::get($moduleID, "lbl_intro");
$header = HTML::select(".header")->item(0);
DOM::append($header, $title);

// Video container
$appVideo = HTML::select(".videoContainer .appCenterVideo")->item(0);

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



// Build popup
$popup = new popup();
return $popup->build($pageContent->get())->getReport();
//#section_end#
?>