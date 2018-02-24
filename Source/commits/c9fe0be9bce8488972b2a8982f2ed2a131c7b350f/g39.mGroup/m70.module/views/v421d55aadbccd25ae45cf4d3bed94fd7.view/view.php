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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \API\Security\account;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "frontendPage", TRUE);

$text = moduleLiteral::get($moduleID, "lbl_subtitle");
$textContainer = HTML::select("h2.subTitle")->item(0);
DOM::append($textContainer, $text);

// Set social text
$text = moduleLiteral::get($moduleID, "lbl_socialText");
$textContainer = HTML::select(".socialText")->item(0);
DOM::append($textContainer, $text);


// Fill in the engine boxes
$boxes = HTML::select(".engineBox");
fillEnginebox($boxes->item(0), "app");
fillEnginebox($boxes->item(1), "web");
fillEnginebox($boxes->item(2), "boss");

function fillEnginebox($engineBox, $class)
{
	HTML::addClass($engineBox, $class);
	
	// Create engine box image
	$img = HTML::create("div", "", "boxImage_".$class, "boxImage");
	HTML::append($engineBox, $img);
	
	// Create engine box title
	$title = HTML::create("h2", "", "boxTitle_".$class, "boxTitle");
	HTML::append($engineBox, $title);
	
	// Create engine box subtitle
	$sub = HTML::create("p", "", "boxSub_".$class, "boxSub");
	HTML::append($engineBox, $sub);
}


// App Engine
$href = url::resolve("apps", "/");
$weblink = $page->getWeblink($href, $content = "App Engine", $target = "_blank");
$boxTitle = HTML::select("#boxTitle_app")->item(0);
HTML::append($boxTitle, $weblink);

$sub = moduleLiteral::get($moduleID, "lbl_appEngineSub");
$boxSub = HTML::select("#boxSub_app")->item(0);
HTML::append($boxSub, $sub);

// Web Engine
$href = url::resolve("web", "/");
$weblink = $page->getWeblink($href, $content = "Web Engine", $target = "_blank");
$boxTitle = HTML::select("#boxTitle_web")->item(0);
HTML::append($boxTitle, $weblink);

$sub = moduleLiteral::get($moduleID, "lbl_webEngineSub");
$boxSub = HTML::select("#boxSub_web")->item(0);
HTML::append($boxSub, $sub);

// Business Engine
$href = url::resolve("boss", "/");
$weblink = $page->getWeblink($href, $content = "Business Engine", $target = "_blank");
$boxTitle = HTML::select("#boxTitle_boss")->item(0);
HTML::append($boxTitle, $weblink);

$sub = moduleLiteral::get($moduleID, "lbl_businessEngineSub");
$boxSub = HTML::select("#boxSub_boss")->item(0);
HTML::append($boxSub, $sub);

return $page->getReport();
//#section_end#
?>