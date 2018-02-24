<?php
//#section#[header]
// Module Declaration
$moduleID = 76;

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
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\Url;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "helpCenter", TRUE);


// Header
$title = moduleLiteral::get($moduleID, "title");
$header = HTML::select("h1.title")->item(0);
DOM::append($header, $title);

$subtitle = moduleLiteral::get($moduleID, "lbl_subtitle");
$header = HTML::select("h2.subtitle")->item(0);
DOM::append($header, $subtitle);

$desc = moduleLiteral::get($moduleID, "lbl_desc");
$header = HTML::select("p.description")->item(0);
DOM::append($header, $desc);


// Trending
$title = moduleLiteral::get($moduleID, "lbl_trendingCategories");
$header = HTML::select(".trending h2.title")->item(0);
DOM::append($header, $title);

// Build categories
$trendingContainer = HTML::select(".trending .categories")->item(0);

$cats = array();
$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/starting/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_gettingStarted");
$cat['class'] = "starting";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/security/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_security");
$cat['class'] = "security";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/social/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_social");
$cat['class'] = "social";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/international/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_international");
$cat['class'] = "international";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/new/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_whatNew");
$cat['class'] = "new";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/business/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_business");
$cat['class'] = "business";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/apps/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_appBuild");
$cat['class'] = "apps";
$cats[] = $cat;

$cat = array();
//$cat['url'] = url::resolve("www", "/help/trending/issues/");
$cat['title'] = moduleLiteral::get($moduleID, "lbl_bugReport");
$cat['class'] = "issues";
$cats[] = $cat;


foreach ($cats as $cat)
{
	$category = getCategory($page, $cat['url'], $cat['title'], $cat['class']);
	DOM::append($trendingContainer, $category);
}

function getCategory($page, $url, $title, $imageClass)
{
	$category = $page->getWeblink($url, $title, "_blank");
	HTML::addClass($category, "trendCategory");
	
	// add image
	$catImage = DOM::create("div", "", "", "trendImage ".$imageClass);
	DOM::append($category, $catImage);
	
	// add title
	$catTitle = DOM::create("h4", $title, "", "trendTitle");
	DOM::append($category, $catTitle);
	
	return $category;
}

// More categories
$title = moduleLiteral::get($moduleID, "lbl_allCategories");
$url = url::resolve("www", "/help/categories/");
$moreWl = $page->getWeblink($url, $title, "_blank");
$more = HTML::select("h4.more")->item(0);
DOM::append($more, $moreWl);


// Fast links
$title = moduleLiteral::get($moduleID, "lbl_fastLinks");
$header = HTML::select(".fastLinks h2.title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_legalNoticies");
$url = url::resolve("www", "/help/legal/");
$fastWl = $page->getWeblink($url, $title, "_blank");
$fast = HTML::select("h3.terms")->item(0);
DOM::append($fast, $fastWl);

$title = moduleLiteral::get($moduleID, "lbl_contact");
$url = url::resolve("www", "/help/contact/");
$fastWl = $page->getWeblink($url, $title, "_blank");
$fast = HTML::select("h3.contact")->item(0);
DOM::append($fast, $fastWl);

return $page->getReport();
//#section_end#
?>