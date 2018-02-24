<?php
//#section#[header]
// Module Declaration
$moduleID = 70;

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
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "rbFrontend", TRUE);

$text = moduleLiteral::get($moduleID, "lbl_subtitle");
$textContainer = HTML::select("h2.subTitle")->item(0);
DOM::append($textContainer, $text);

// Set social text
$text = moduleLiteral::get($moduleID, "lbl_socialText");
$textContainer = HTML::select(".socialText")->item(0);
DOM::append($textContainer, $text);


/*
// Head links
$text = moduleLiteral::get($moduleID, "lbl_discoverTitle");
$title = HTML::select(".headLinks .discover .title")->item(0);
DOM::append($title, $text);

$text = moduleLiteral::get($moduleID, "lbl_discoverSubtitle");
$url = url::resolve("www", "/discover.php");
$wl = $page->getWeblink($url, $text, "_self");
$title = HTML::select(".headLinks .discover .subtitle")->item(0);
DOM::append($title, $wl);


$text = moduleLiteral::get($moduleID, "lbl_careersTitle");
$title = HTML::select(".headLinks .careers .title")->item(0);
DOM::append($title, $text);

$text = moduleLiteral::get($moduleID, "lbl_careersSubtitle");
$url = url::resolve("www", "/careers.php#cintern");
$wl = $page->getWeblink($url, $text, "_self");
$title = HTML::select(".headLinks .careers .subtitle")->item(0);
DOM::append($title, $wl);
*/


// Engines title
$title = moduleLiteral::get($moduleID, "lbl_enginesTitle");
$header = HTML::select(".engines .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_enginesSub");
$header = HTML::select(".engines .subtitle")->item(0);
DOM::append($header, $title);


// App Engine
$href = url::resolve("apps", "/");
setEngineBox("app", $href, $moduleID, "lbl_appEngineSub");

// Web Engine
$href = url::resolve("web", "/");
setEngineBox("web", $href, $moduleID, "lbl_webEngineSub");

// Business Engine
$href = url::resolve("boss", "/");
setEngineBox("boss", $href, $moduleID, "lbl_businessEngineSub");


// Developer
$title = moduleLiteral::get($moduleID, "lbl_developers_developer_title");
$header = HTML::select(".developers .developer .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_developers_developer_desc");
$url = url::resolve("developer", "/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".developers .developer .subtitle")->item(0);
DOM::append($header, $wl);


$title = moduleLiteral::get($moduleID, "lbl_developers_support_title");
$header = HTML::select(".developers .support .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_developers_support_desc");
$url = url::resolve("developer", "/support/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".developers .support .subtitle")->item(0);
DOM::append($header, $wl);



// International
$title = moduleLiteral::get($moduleID, "lbl_rbInternational");
$header = HTML::select(".international .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_internationalSub");
$url = url::resolve("www", "/international.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".international .subtitle")->item(0);
DOM::append($header, $wl);


// Footer logo
$title = DOM::create("span", "Redback");
$header = HTML::select(".footLinks .rb .text")->item(0);
DOM::append($header, $title);

// Footlinks
$title = DOM::create("span", "Redback");
$header = HTML::select(".footLinks .linkCol.general .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_gen_about");
$url = url::resolve("www", "/discover.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.general .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_gen_contact");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.general .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_gen_feedback");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.general .listItem")->item(2);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_gen_report");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.general .listItem")->item(3);
DOM::append($header, $wl);



$title = moduleLiteral::get($moduleID, "lbl_fl_soc_header");
$header = HTML::select(".footLinks .linkCol.social .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_soc_careers");
$url = url::resolve("www", "/careers.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.social .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_soc_intern");
$url = url::resolve("www", "/careers.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.social .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_soc_partners");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.social .listItem")->item(2);
DOM::append($header, $wl);


$title = moduleLiteral::get($moduleID, "lbl_fl_dev_header");
$header = HTML::select(".footLinks .linkCol.dev .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_dev_appEngine");
$url = url::resolve("apps", "/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.dev .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_dev_webEngine");
$url = url::resolve("web", "/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.dev .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_dev_docs");
$url = url::resolve("developer", "/docs/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.dev .listItem")->item(2);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_dev_tools");
$url = url::resolve("developer", "/tools/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.dev .listItem")->item(3);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_dev_support");
$url = url::resolve("developer", "/support/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.dev .listItem")->item(4);
DOM::append($header, $wl);



return $page->getReport();


function setEngineBox($class, $url, $moduleID, $subLiteral)
{
	// Set href
	$box = HTML::select(".engineBox.".$class)->item(0);
	DOM::attr($box, "href", $url);
	DOM::attr($box, "target", "_blank");
	
	// Set subtitle
	$sub = moduleLiteral::get($moduleID, $subLiteral);
	$boxSub = HTML::select(".engineBox.".$class." .boxSub")->item(0);
	HTML::append($boxSub, $sub);
}
//#section_end#
?>