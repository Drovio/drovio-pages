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
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("freeLayout");
$page->build($pageTitle, "appCenterPage");

// Registration bar
$regBar = DOM::create("div", "", "", "topBar");
$page->appendToSection("main", $regBar);

$logoContainer = DOM::create("div", "", "", "logoContainer");
DOM::append($regBar, $logoContainer);

$regContainer = DOM::create("div", "", "", "topContainer");
DOM::append($regBar, $regContainer);

// Reg Button
$regButtonTitle = moduleLiteral::get($moduleID, "lbl_pageUc");
$regButton = DOM::create("div", $regButtonTitle, "", "regBtn");
DOM::append($regContainer, $regButton);

// Subtitle
$title = moduleLiteral::get($moduleID, "lbl_subTitle");
$subTitle = DOM::create("p");
DOM::append($subTitle, $title);
DOM::append($logoContainer, $subTitle);

// Global page container
$globalContainer = DOM::create("div", "", "globalContainer", "globalContainer");
$page->appendToSection("main", $globalContainer);


// Video container
$videoCont = DOM::create("div", "", "vdContainer", "vdContainer");
DOM::append($globalContainer, $videoCont);

$appVideo = DOM::create("video");
DOM::attr($appVideo, "width", "640");
DOM::attr($appVideo, "controls", "1");
DOM::append($videoCont, $appVideo);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/videos/appCenter/promo.webm");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/webm");
DOM::append($appVideo, $vSource);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/videos/appCenter/promo.mp4");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/mp4");
DOM::append($appVideo, $vSource);

// No support
$noSupport = DOM::create("p", "Your browser doesn't support this video yet. Please use firefox or chrome.");
DOM::append($appVideo, $noSupport);

return $page->getReport();
//#section_end#
?>