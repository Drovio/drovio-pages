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
importer::import("API", "Literals");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;

// Module Page
$page = new HTMLModulePage();

// Build Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devCenterPage", TRUE);


// Subtitle text
$title = moduleLiteral::get($moduleID, "lbl_subText");
$header = HTML::select(".subText")->item(0);
DOM::append($header, $title);


// Moto
$title = moduleLiteral::get($moduleID, "lbl_devMoto");
$header = HTML::select(".line")->item(0);
DOM::append($header, $title);

/*
// Menu links
$title = moduleLiteral::get($moduleID, "lbl_menuHome");
$href = url::resolve("developer", "/");
$weblink = $page->getWeblink($href, $title);
$menu = HTML::select(".devMenuItem.home")->item(0);
DOM::append($menu, $weblink);

$title = moduleLiteral::get($moduleID, "lbl_menuNews");
$href = url::resolve("developer", "/news/");
$weblink = $page->getWeblink($href, $title);
$menu = HTML::select(".devMenuItem.news")->item(0);
DOM::append($menu, $weblink);

$title = moduleLiteral::get($moduleID, "lbl_menuEvents");
$href = url::resolve("developer", "/events/");
$weblink = $page->getWeblink($href, $title);
$menu = HTML::select(".devMenuItem.events")->item(0);
DOM::append($menu, $weblink);
*/

// Enroll menu item
if (account::validate() && !privileges::accountToGroup("DEVELOPER"))
{
	$devMenu = HTML::select(".devMenu")->item(0);
	$menu = HTML::select(".devMenuItem.events")->item(0);
	$connect = $menu->cloneNode();
	
	HTML::removeClass($connect, "events");
	HTML::addClass($connect, "connect");
	DOM::append($devMenu, $connect);
	
	$title = moduleLiteral::get($moduleID, "lbl_enroll");
	$href = url::resolve("developer", "/enroll.php");
	$weblink = $page->getWeblink($href, $title);
	DOM::append($connect, $weblink);
}


// Features
setFeature($moduleID, "values");
setFeature($moduleID, "space");
setFeature($moduleID, "web");
setFeature($moduleID, "apps");
setFeature($moduleID, "projdev");
setFeature($moduleID, "projft");


function setFeature($moduleID, $class)
{
	// Set title
	$context = moduleLiteral::get($moduleID, "lbl_features_".$class."_title");
	$header = HTML::select(".fItem.".$class." .title")->item(0);
	DOM::append($header, $context);
	
	// Set subtitle
	$context = moduleLiteral::get($moduleID, "lbl_features_".$class."_desc");
	$header = HTML::select(".fItem.".$class." .sub")->item(0);
	DOM::append($header, $context);
}



// App Engine
$title = moduleLiteral::get($moduleID, "lbl_appEngine_desc");
$header = HTML::select(".dvc.apps .desc")->item(0);
DOM::append($header, $title);

// Links
$title = moduleLiteral::get($moduleID, "lbl_appEngine_more_appcenter");
$href = url::resolve("apps", "/");
$weblink = $page->getWeblink($href, $title);
$header = HTML::select(".dvc.apps .more .appcenter")->item(0);
DOM::append($header, $weblink);

$title = moduleLiteral::get($moduleID, "lbl_more_ref");
$href = url::resolve("developer", "/docs/");
$weblink = $page->getWeblink($href, $title);
$header = HTML::select(".dvc.apps .more .ref")->item(0);
DOM::append($header, $weblink);

$title = moduleLiteral::get($moduleID, "lbl_more_learn");
$href = url::resolve("www", "/help/");
$weblink = $page->getWeblink($href, $title);
$header = HTML::select(".dvc.apps .more .learn")->item(0);
DOM::append($header, $weblink);


// Web Engine
$title = moduleLiteral::get($moduleID, "lbl_webEngine_desc");
$header = HTML::select(".dvc.web .desc")->item(0);
DOM::append($header, $title);


$title = moduleLiteral::get($moduleID, "lbl_more_ref");
$href = url::resolve("developer", "/docs/");
$weblink = $page->getWeblink($href, $title);
$header = HTML::select(".dvc.web .more .ref")->item(0);
DOM::append($header, $weblink);

$title = moduleLiteral::get($moduleID, "lbl_more_learn");
$href = url::resolve("www", "/help/");
$weblink = $page->getWeblink($href, $title);
$header = HTML::select(".dvc.web .more .learn")->item(0);
DOM::append($header, $weblink);


// Templates and extensions
$title = moduleLiteral::get($moduleID, "lbl_web_templates");
$header = HTML::select(".extra .box.templates .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_web_extensions");
$header = HTML::select(".extra .box.extensions .title")->item(0);
DOM::append($header, $title);



// Footlinks
$title = moduleLiteral::get($moduleID, "lbl_fl_engine_header");
$header = HTML::select(".footLinks .linkCol.engine .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_engine_enroll");
$url = url::resolve("developer", "/enroll.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.engine .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_engine_discover");
$url = url::resolve("www", "/discover.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.engine .listItem")->item(1);
DOM::append($header, $wl);



$title = moduleLiteral::get($moduleID, "lbl_fl_ref_header");
$header = HTML::select(".footLinks .linkCol.reference .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_ref_docs");
$url = url::resolve("developer", "/docs/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.reference .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_ref_tools");
$url = url::resolve("developer", "/tools/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.reference .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_ref_api");
$url = url::resolve("developer", "/tools/api/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.reference .listItem")->item(2);
DOM::append($header, $wl);


$title = moduleLiteral::get($moduleID, "lbl_fl_support_header");
$header = HTML::select(".footLinks .linkCol.support .colHeader")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_fl_support_genHelp");
$url = url::resolve("www", "/help/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.support .listItem")->item(0);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_support_support");
$url = url::resolve("developer", "/support/");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.support .listItem")->item(1);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_support_contact");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.support .listItem")->item(2);
DOM::append($header, $wl);

$title = moduleLiteral::get($moduleID, "lbl_fl_support_reportProblem");
$url = url::resolve("www", "/help/contact.php");
$wl = $page->getWeblink($url, $title, "_blank");
$header = HTML::select(".footLinks .linkCol.support .listItem")->item(3);
DOM::append($header, $wl);



return $page->getReport();
//#section_end#
?>