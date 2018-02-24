<?php
//#section#[header]
// Module Declaration
$moduleID = 193;

// Inner Module Codes
$innerModules = array();
$innerModules['statusPage'] = 192;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\misc\platformStatus;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridView;
use \DEV\Profiler\status;


// Build Module Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page = new HTMLModulePage();
$page->build($title, "devSupportPage", TRUE);


$title = moduleLiteral::get($moduleID, "title");
$header = HTML::select("h1.title")->item(0);
DOM::append($header, $title);

$subtitle = moduleLiteral::get($moduleID, "lbl_subtitle");
$header = HTML::select("h2.subtitle")->item(0);
DOM::append($header, $subtitle);

$desc = moduleLiteral::get($moduleID, "lbl_desc");
$header = HTML::select("p.description")->item(0);
DOM::append($header, $desc);


// Status
$pStatus = new status();
$status = $pStatus->getStatus();
$statusBar = HTML::select(".statusBar")->item(0);
$statusDesc = HTML::select(".statusBar h3.statusDesc a")->item(0);
$url = url::resolve("developer", "/tools/status/");
HTML::attr($statusDesc, "href", $url);
if ($status['code'] == status::STATUS_OK)
{
	HTML::addClass($statusBar, "healthy");
	$desc = moduleLiteral::get($innerModules['statusPage'], "lbl_healthyPlatform");
}
else
{
	HTML::addClass($statusBar, "sick");
	$desc = $status['description'];
}
DOM::append($statusDesc, $desc);

// Links

// News
$title = moduleLiteral::get($moduleID, "lbl_devNews");
$header = HTML::select(".devNews h3.title")->item(0);
DOM::append($header, $title);

$p = DOM::create("p", "There are no news at the moment.");
$dvNews = HTML::select(".devNews")->item(0);
DOM::append($dvNews, $p);

// Resources
$title = moduleLiteral::get($moduleID, "lbl_devResources");
$header = HTML::select(".devRsrc h3.title")->item(0);
DOM::append($header, $title);

$linkList = HTML::select(".devRsrc .linkList")->item(0);
$url = url::resolve("www", "/help/developer/");
$title = moduleLiteral::get($moduleID, "lbl_devHelpCenter");
$wl = $page->getWeblink($url, $title, "_blank");
$headerA = DOM::create("h4", $wl);
DOM::append($linkList, $headerA);

$linkList = HTML::select(".devRsrc .linkList")->item(0);
$url = url::resolve("developer", "/docs/");
$title = moduleLiteral::get($moduleID, "lbl_devDocsRef");
$wl = $page->getWeblink($url, $title, "_blank");
$headerA = DOM::create("h4", $wl);
DOM::append($linkList, $headerA);

// Tools
$title = moduleLiteral::get($moduleID, "lbl_devTools");
$header = HTML::select(".devTools h3.title")->item(0);
DOM::append($header, $title);

$linkList = HTML::select(".devTools .linkList")->item(0);
$url = url::resolve("developer", "/tools/status/");
$title = moduleLiteral::get($moduleID, "lbl_platformStatus");
$wl = $page->getWeblink($url, $title, "_blank");
$headerA = DOM::create("h4", $wl);
DOM::append($linkList, $headerA);

$linkList = HTML::select(".devTools .linkList")->item(0);
$url = url::resolve("developer", "/tools/api/");
$title = moduleLiteral::get($moduleID, "lbl_devPublicApi");
$wl = $page->getWeblink($url, $title, "_blank");
$headerA = DOM::create("h4", $wl);
DOM::append($linkList, $headerA);

$linkList = HTML::select(".devTools .linkList")->item(0);
$url = url::resolve("developer", "/bugs/");
$title = moduleLiteral::get($moduleID, "lbl_platformBugs");
$wl = $page->getWeblink($url, $title, "_blank");
$headerA = DOM::create("h4", $wl);
DOM::append($linkList, $headerA);


/*
// The road so far
$title = moduleLiteral::get($moduleID, "lbl_roadmapTitle");
$header = HTML::select(".roadmap h2.title")->item(0);
DOM::append($header, $title);
*/

return $page->getReport();
//#section_end#
?>