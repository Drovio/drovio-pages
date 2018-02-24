<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['loginPage'] = 66;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;

// Module Page
$page = new HTMLModulePage();

// Build Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devCenterPage", TRUE);


// Page title
$title = moduleLiteral::get($moduleID, "title");
$header = HTML::select("h1.title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_subtitle");
$header = HTML::select("h2.subtitle")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_desc");
$header = HTML::select(".description")->item(0);
DOM::append($header, $title);


// Enroll link
if (account::validate() && privileges::accountToGroup("DEVELOPER"))
{
	$connect = HTML::select(".connect")->item(0);
	DOM::replace($connect, NULL);
}
else
{
	$link = HTML::select(".connect a.cna")->item(0);
	$url = url::resolve("developer", "/enroll.php");
	DOM::attr($link, "href", $url);
	
	$linkTitle = HTML::select(".connect h3.title")->item(0);
	$title = moduleLiteral::get($moduleID, "lbl_enroll");
	DOM::append($linkTitle, $title);
}



// Tiles
$title = moduleLiteral::get($moduleID, "lbl_dvcApps_desc");
$desc = HTML::select(".dvc.apps h3.desc")->item(0);
DOM::append($desc, $title);

$more = moduleLiteral::get($moduleID, "lbl_learnMore");
$learnMore = HTML::select(".dvc.apps p.more")->item(0);
$url = url::resolve("developer", "/docs/apps/");
$wl = $page->getWeblink($url, $more, "_blank");
DOM::append($learnMore, $wl);


$title = moduleLiteral::get($moduleID, "lbl_dvcExts_title");
$header = HTML::select(".dvc.exts h2.title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_dvcExts_desc");
$desc = HTML::select(".dvc.exts h3.desc")->item(0);
DOM::append($desc, $title);

$learnMore = HTML::select(".dvc.exts p.more")->item(0);
$url = url::resolve("developer", "/docs/web/extensions/");
$wl = $page->getWeblink($url, $more->cloneNode(TRUE), "_blank");
DOM::append($learnMore, $wl);


$title = moduleLiteral::get($moduleID, "lbl_dvcTempl_title");
$header = HTML::select(".dvc.tmpl h2.title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_dvcTempl_desc");
$desc = HTML::select(".dvc.tmpl h3.desc")->item(0);
DOM::append($desc, $title);

$learnMore = HTML::select(".dvc.tmpl p.more")->item(0);
$url = url::resolve("developer", "/docs/web/templates/");
$wl = $page->getWeblink($url, $more->cloneNode(TRUE), "_blank");
DOM::append($learnMore, $wl);

return $page->getReport();
//#section_end#
?>